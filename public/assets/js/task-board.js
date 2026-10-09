// Enhance the server-rendered filters without changing their URLs or fallback behavior.
(() => {
    const board = document.getElementById('task-board-section');
    if (!board || !window.fetch || !window.DOMParser) return;

    let inFlight;
    async function showBoard(url, historyMode, focusTarget) {
        inFlight?.abort();
        const request = new AbortController();
        inFlight = request;
        board.setAttribute('aria-busy', 'true');
        board.classList.add('is-loading');
        try {
            const response = await fetch(url, {
                signal: request.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!response.ok) throw new Error('Task board request failed');
            const nextPage = new DOMParser().parseFromString(await response.text(), 'text/html');
            const nextBoard = nextPage.getElementById('task-board-section');
            if (!nextBoard) throw new Error('Task board response was incomplete');
            board.innerHTML = nextBoard.innerHTML;
            if (historyMode === 'push') history.pushState({}, '', url);
            if (focusTarget === 'status') board.querySelector('.filter-chip[aria-current="true"]')?.focus();
            if (focusTarget === 'date') board.querySelector('.date-filter')?.focus();
            if (focusTarget === 'search') board.querySelector('#task-query')?.focus();
        } catch (error) {
            if (error.name !== 'AbortError') location.assign(url);
        } finally {
            if (inFlight === request) {
                board.removeAttribute('aria-busy');
                board.classList.remove('is-loading');
                inFlight = undefined;
            }
        }
    }

    board.addEventListener('click', (event) => {
        const link = event.target.closest('.filter-chip, .date-filter, .task-results a');
        if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        const target = new URL(link.href, location.href);
        if (target.origin !== location.origin) return;
        event.preventDefault();
        showBoard(target.href, 'push', link.classList.contains('filter-chip') ? 'status' : link.classList.contains('date-filter') ? 'date' : 'search');
    });
    board.addEventListener('submit', (event) => {
        const form = event.target.closest('.task-search');
        if (!form) return;
        event.preventDefault();
        const target = new URL(form.action);
        target.search = new URLSearchParams(new FormData(form)).toString();
        showBoard(target.href, 'push', 'search');
    });
    window.addEventListener('popstate', () => showBoard(location.href, 'none', null));
})();
