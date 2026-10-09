function openModal(dialog) {
    if (!(dialog instanceof HTMLDialogElement) || dialog.open) return;
    dialog.showModal();
    document.body.classList.add('overflow-hidden');
    const firstField = dialog.querySelector('[aria-invalid="true"], input:not([type="hidden"]), select');
    firstField?.focus();
}

document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-open-modal]');
    if (opener) openModal(document.getElementById(opener.dataset.openModal));

    const closer = event.target.closest('[data-close-modal]');
    if (closer) closer.closest('dialog')?.close();
});

document.querySelectorAll('dialog').forEach((dialog) => {
    dialog.addEventListener('close', () => {
        if (!document.querySelector('dialog[open]')) document.body.classList.remove('overflow-hidden');
    });
    dialog.addEventListener('click', (event) => {
        const bounds = dialog.getBoundingClientRect();
        if (event.target === dialog && (
            event.clientX < bounds.left || event.clientX > bounds.right ||
            event.clientY < bounds.top || event.clientY > bounds.bottom
        )) dialog.close();
    });
});

openModal(document.querySelector('dialog[data-auto-open]'));
