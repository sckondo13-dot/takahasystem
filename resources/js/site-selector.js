export function initSiteSelector() {

    const site = document.getElementById('site_id');

    if (!site) return;

    /**
     * 日報登録
     */
    const workDate = document.getElementById('work_date');

    if (workDate) {

        workDate.addEventListener('change', () => {
            loadSites(workDate.value, 'date');
        });

    }

    /**
     * 現場月報
     */
    const month = document.getElementById('month');

    if (month) {

        month.addEventListener('change', () => {
            loadSites(month.value, 'month');
        });

    }

    async function loadSites(value, type) {

        if (!value) return;

        const response = await fetch(`/api/sites?${type}=${value}`);

        const sites = await response.json();

        site.innerHTML =
            '<option value="">選択してください</option>';

        sites.forEach(s => {

            site.insertAdjacentHTML(
                'beforeend',
                `<option value="${s.id}">${s.name}</option>`
            );

        });

    }

}