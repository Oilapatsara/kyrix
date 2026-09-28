import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';
import { test } from 'node:test';
import assert from 'node:assert/strict';

const source = readFileSync(new URL('../resources/views/layouts/owner.blade.php', import.meta.url), 'utf8')
    .match(/<script>([\s\S]*?)<\/script>/)[1];

function setup(value = '{}', blocked = false) {
    const handlers = {};
    const windowHandlers = {};
    const values = new Map([['kyrix_read_notifs:1', value]]);
    const element = () => {
        const classes = new Set();
        return {
            style: {}, dataset: {}, attributes: {}, textContent: '',
            classList: {
                contains: name => classes.has(name),
                remove: name => classes.delete(name),
                toggle(name, force = !classes.has(name)) {
                    if (force) classes.add(name); else classes.delete(name);
                },
            },
            setAttribute(name, val) { this.attributes[name] = String(val); },
            addEventListener() {}, focus() { this.focused = true; },
        };
    };
    const nodes = Object.fromEntries(['notifDropdownWrapper', 'notifBellBtn', 'notifDropdownMenu',
        'notifBadgeCount', 'notifHeaderCount'].map(id => [id, element()]));
    nodes.notifDropdownWrapper.dataset.ownerId = '1';
    const item = element();
    item.getAttribute = () => 'return-5';
    item.querySelector = () => null;
    const context = {
        document: {
            getElementById: id => nodes[id], querySelectorAll: () => [item],
            addEventListener: (event, callback) => { handlers[event] = callback; },
        },
        window: { addEventListener: (event, callback) => { windowHandlers[event] = callback; } },
        localStorage: {
            getItem(key) { if (blocked) throw Error('Blocked'); return values.get(key); },
            setItem(key, val) { if (blocked) throw Error('Blocked'); values.set(key, val); },
        },
    };
    runInNewContext(source, context);
    handlers.DOMContentLoaded();
    return { context, nodes, handlers, windowHandlers, values, item };
}

test('bell opens without submitting a form and closes with Escape or an outside click', () => {
    const { context, nodes, handlers, windowHandlers } = setup();
    let prevented = false;
    const event = { preventDefault() { prevented = true; }, stopPropagation() {} };
    context.toggleNotifDropdown(event);
    assert.equal(prevented, true);
    assert.equal(nodes.notifBellBtn.attributes['aria-expanded'], 'true');
    handlers.keydown({ key: 'Escape' });
    assert.equal(nodes.notifBellBtn.attributes['aria-expanded'], 'false');
    assert.equal(nodes.notifBellBtn.focused, true);
    context.toggleNotifDropdown(event);
    windowHandlers.click();
    assert.equal(nodes.notifDropdownMenu.classList.contains('show'), false);
});

test('reading a notification works with unavailable or malformed storage', () => {
    for (const [value, blocked] of [['{}', true], ['null', false], ['[]', false], ['broken', false]]) {
        const { context, nodes, item } = setup(value, blocked);
        assert.doesNotThrow(() => context.markNotifRead('return-5'));
        assert.equal(item.classList.contains('is-read'), true);
        assert.equal(nodes.notifBadgeCount.style.display, 'none');
    }
});

test('read state expires after 12 hours and is stored separately per owner', () => {
    const { context, item, values } = setup(JSON.stringify({ 'return-5': Date.now() - 13 * 3600000 }));
    assert.equal(item.classList.contains('is-read'), false);
    context.markNotifRead('return-5');
    assert.ok(JSON.parse(values.get('kyrix_read_notifs:1'))['return-5']);
    assert.equal(values.has('kyrix_read_notifs'), false);
});
