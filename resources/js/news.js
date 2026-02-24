document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('searchForm');
    const input = document.getElementById('newsSearch');
    if (!form || !input) {
        return;
    }

    let timer = null;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            if (input.value.trim().length === 0 || input.value.trim().length >= 2) {
                form.submit();
            }
        }, 550);
    });
});
