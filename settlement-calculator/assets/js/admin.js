(function () {
  'use strict';
  const root = document.querySelector('.sc-admin');
  if (!root) return;
  const groups = root.querySelector('[data-faq-groups]');
  const groupTemplate = root.querySelector('[data-group-template]').innerHTML;
  const itemTemplate = root.querySelector('[data-item-template]').innerHTML;
  root.addEventListener('click', (event) => {
    const addGroup = event.target.closest('[data-add-group]');
    const removeGroup = event.target.closest('[data-remove-group]');
    const addItem = event.target.closest('[data-add-item]');
    const removeItem = event.target.closest('[data-remove-item]');
    if (addGroup) { const index = root.querySelectorAll('[data-faq-group]').length; groups.insertAdjacentHTML('beforeend', groupTemplate.replaceAll('__GROUP__', index)); }
    if (removeGroup) removeGroup.closest('[data-faq-group]').remove();
    if (addItem) { const group = addItem.closest('[data-faq-group]'); const match = group.querySelector('input[name*="[label]"]').name.match(/faqs\]\[([^\]]+)/); const index = group.querySelectorAll('[data-faq-item]').length; group.querySelector('[data-faq-items]').insertAdjacentHTML('beforeend', itemTemplate.replaceAll('__GROUP__', match[1]).replaceAll('__ITEM__', index)); }
    if (removeItem) removeItem.closest('[data-faq-item]').remove();
  });
}());
