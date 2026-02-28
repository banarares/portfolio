(() => {
    const sentinel = document.getElementById('load-more-sentinel');
    if (!sentinel) return;

    const projectsList = document.getElementById('projects-list');
    if (!projectsList) return;

    const observer = new IntersectionObserver(async (entries) => {
        if (entries[0].isIntersecting && sentinel.dataset.hasMore === '1') {
            const nextPage = parseInt(sentinel.dataset.nextPage, 10);
            const perPage = parseInt(sentinel.dataset.perPage, 10);
            const activeTag = new URLSearchParams(window.location.search).get('tag');
            const tagQuery = activeTag ? `&tag=${encodeURIComponent(activeTag)}` : '';

            try {
                const response = await fetch(`/?page=${nextPage}&perPage=${perPage}&ajax=1${tagQuery}`);
                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();
                data.items.forEach((project) => {
                    const card = document.createElement('a');
                    card.className = 'project-card reveal';
                    card.href = '/project/' + encodeURIComponent(project.slug);

                    if (project.image_path) {
                        const imgWrap = document.createElement('div');
                        imgWrap.className = 'project-image';
                        const img = document.createElement('img');
                        img.src = project.image_path;
                        img.alt = '';
                        imgWrap.appendChild(img);
                        card.appendChild(imgWrap);
                    }

                    const content = document.createElement('div');
                    content.className = 'project-content';

                    const topline = document.createElement('div');
                    topline.className = 'project-topline';

                    const title = document.createElement('h3');
                    title.textContent = project.title;
                    topline.appendChild(title);

                    if (project.category_name) {
                        const badge = document.createElement('span');
                        badge.className = 'badge';
                        badge.textContent = 'Category: ' + project.category_name;
                        topline.appendChild(badge);
                    }

                    content.appendChild(topline);

                    if (project.summary) {
                        const summary = document.createElement('p');
                        summary.textContent = project.summary;
                        content.appendChild(summary);
                    }

                    card.appendChild(content);
                    projectsList.appendChild(card);
                });

                sentinel.dataset.hasMore = data.hasMore ? '1' : '0';
                sentinel.dataset.nextPage = data.nextPage;
            } catch (error) {
                console.error('Error loading more projects:', error);
            }
        }
    });

    observer.observe(sentinel);
})();