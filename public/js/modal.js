function switchModal(closeId, openId) {
    const closeModal = bootstrap.Modal.getInstance(document.getElementById(closeId));
    if (closeModal) {
        closeModal.hide()
    }
    setTimeout(() => {
        const openModal = new bootstrap.Modal(document.getElementById(openId));
        openModal.show()
    }, 300);
}