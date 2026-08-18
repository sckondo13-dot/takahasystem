export function initInvoiceSiteSelector() {

    const client = document.getElementById('client_id');

    const month = document.querySelector('input[name="month"]');

    const site = document.getElementById('site_id');

    if (!client || !month || !site) return;

    async function loadSites() {

        if (!client.value || !month.value) {

            site.innerHTML =
                '<option>先に元請・月を選択してください</option>';

            return;
        }

        const response = await fetch(
            `/api/sites?client_id=${client.value}&month=${month.value}`
        );

        const sites = await response.json();

        site.innerHTML = '';

        if (sites.length === 0) {

            site.innerHTML =
                '<option>対象現場なし</option>';

            return;
        }

        site.insertAdjacentHTML(
            'beforeend',
            '<option value="">選択してください</option>'
        );

        sites.forEach(s => {

            site.insertAdjacentHTML(

                'beforeend',

                `<option value="${s.id}">${s.name}</option>`

            );

        });

    }

    client.addEventListener('change', loadSites);

    month.addEventListener('change', loadSites);

    loadSites();

    site.addEventListener('change', async () => {

        if (!site.value) return;

        const response = await fetch(

            `/api/invoice-summary?site_id=${site.value}&month=${month.value}`

        );

        const data = await response.json();

        document
            .getElementById('summaryArea')
            .classList.remove('hidden');

        document
            .getElementById('man_hours')
            .innerHTML = data.man_hours;

        document
            .getElementById('unit_price')
            .innerHTML =
            Number(data.unit_price).toLocaleString();

        document
            .getElementById('sales')
            .innerHTML =
            Number(data.sales).toLocaleString();

        document
            .getElementById('transportation')
            .innerHTML =
            Number(data.transportation).toLocaleString();
            
        document.getElementById('sales_input').value =
            data.sales;

        document.getElementById('transportation_input').value =
            data.transportation;

        document.getElementById('man_hours_input').value =
            data.man_hours;

        document.getElementById('unit_price_input').value =
            data.unit_price;

    });
}