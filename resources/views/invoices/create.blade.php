<x-app-layout>

    <div class="max-w-7xl mx-auto py-10 px-4">

        <h1 class="text-2xl font-bold mb-8">
            請求書作成
        </h1>

        @if ($errors->any())
        <div class="mb-6 rounded border border-red-300 bg-red-50 p-4 text-red-700">
            <div class="font-bold mb-2">
                入力内容を確認してください。
            </div>

            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form
            method="POST"
            action="{{ route('invoices.store') }}"
            id="invoice-form">
            @csrf

            {{-- =========================================================
            請求基本情報
        ========================================================== --}}

            <div class="bg-white rounded-lg shadow p-6 mb-8">

                <h2 class="text-lg font-bold border-b pb-3 mb-6">
                    請求基本情報
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- 元請 --}}
                    <div>
                        <label
                            for="client_id"
                            class="block font-medium mb-1">
                            元請
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="client_id"
                            name="client_id"
                            required
                            class="border rounded w-full px-3 py-2">
                            <option value="">
                                選択してください
                            </option>

                            @foreach ($clients as $client)
                            <option
                                value="{{ $client->id }}"
                                @selected(old('client_id')==$client->id)
                                >
                                {{ $client->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- 請求月 --}}
                    <div>
                        <label
                            for="month"
                            class="block font-medium mb-1">
                            請求月
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="month"
                            id="month"
                            name="month"
                            value="{{ old('month', $invoiceMonth) }}"
                            required
                            class="border rounded w-full px-3 py-2">
                    </div>


                    {{-- 請求日 --}}
                    <div>
                        <label
                            for="invoice_date"
                            class="block font-medium mb-1">
                            請求日
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="date"
                            id="invoice_date"
                            name="invoice_date"
                            value="{{ old('invoice_date', $invoiceDate) }}"
                            required
                            class="border rounded w-full px-3 py-2">
                    </div>


                    {{-- 支払期限 --}}
                    <div>
                        <label
                            for="payment_due"
                            class="block font-medium mb-1">
                            お支払期限
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="date"
                            id="payment_due"
                            name="payment_due"
                            value="{{ old('payment_due', $paymentDue) }}"
                            required
                            class="border rounded w-full px-3 py-2">
                    </div>

                </div>


                {{-- =====================================================
                送付状
            ====================================================== --}}

                <div class="mt-8">

                    <label class="block font-medium mb-2">
                        送付状
                        <span class="text-red-500">*</span>
                    </label>

                    <div class="flex gap-6">

                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                name="send_cover_letter"
                                value="yes"
                                class="mr-2"
                                @checked(old('send_cover_letter', 'no' )==='yes' )>
                            する
                        </label>

                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                name="send_cover_letter"
                                value="no"
                                class="mr-2"
                                @checked(old('send_cover_letter', 'no' )==='no' )>
                            しない
                        </label>

                    </div>


                    <div
                        id="cover-letter-area"
                        class="mt-4 hidden">

                        <div
                            id="cover-letter-list"
                            class="space-y-3">

                            <div class="flex gap-2 cover-letter-row">

                                <input
                                    type="text"
                                    name="cover_letters[]"
                                    placeholder="書類名を入力してください"
                                    class="border rounded px-3 py-2 flex-1"
                                    value="{{ old('cover_letters.0') }}">

                                <button
                                    type="button"
                                    class="remove-cover-letter border rounded px-3 py-2 text-red-600 hover:bg-red-50">
                                    削除
                                </button>

                            </div>

                        </div>

                        <button
                            type="button"
                            id="add-cover-letter"
                            class="mt-3 border rounded px-4 py-2 hover:bg-gray-50">
                            ＋追加
                        </button>

                    </div>

                </div>


                {{-- 備考 --}}
                <div class="mt-8">

                    <label
                        for="remarks"
                        class="block font-medium mb-1">
                        備考
                    </label>

                    <textarea
                        id="remarks"
                        name="remarks"
                        rows="4"
                        class="border rounded w-full px-3 py-2">{{ old('remarks') }}</textarea>

                </div>

            </div>


            {{-- =========================================================
            請求内訳
        ========================================================== --}}

            <div class="bg-white rounded-lg shadow p-6 mb-8">

                <div class="flex items-center justify-between border-b pb-3 mb-6">

                    <h2 class="text-lg font-bold">
                        請求内訳
                    </h2>

                    <button
                        type="button"
                        id="add-detail"
                        class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-gray-700">
                        ＋ 行追加
                    </button>

                </div>


                <div
                    id="detail-list"
                    class="space-y-6">

                    {{-- JavaScriptで追加 --}}

                </div>

            </div>


            {{-- =========================================================
            請求合計
        ========================================================== --}}

            <div class="bg-white rounded-lg shadow p-6 mb-8">

                <h2 class="text-lg font-bold border-b pb-3 mb-6">
                    請求合計
                </h2>

                <div class="max-w-md ml-auto space-y-3">

                    <div class="flex justify-between">
                        <span>
                            課税対象合計
                        </span>

                        <span>
                            <span id="taxable-subtotal">0</span> 円
                        </span>
                    </div>


                    <div class="flex justify-between">
                        <span>
                            非課税対象合計
                        </span>

                        <span>
                            <span id="exempt-subtotal">0</span> 円
                        </span>
                    </div>


                    <div class="flex justify-between">
                        <span>
                            消費税（10％）
                        </span>

                        <span>
                            <span id="tax-total">0</span> 円
                        </span>
                    </div>


                    <div class="border-t pt-3 flex justify-between text-lg font-bold">
                        <span>
                            合計
                        </span>

                        <span>
                            <span id="grand-total">0</span> 円
                        </span>
                    </div>

                </div>

            </div>


            {{-- =========================================================
            ボタン
        ========================================================== --}}

            <div class="flex justify-end gap-3">

                <a
                    href="{{ route('invoices.index') }}"
                    class="border rounded px-6 py-3 hover:bg-gray-50">
                    キャンセル
                </a>

                <button
                    type="submit"
                    class="bg-blue-600 text-white rounded px-6 py-3 hover:bg-blue-700">
                    請求書を作成
                </button>

            </div>

        </form>

    </div>


    {{-- =============================================================
    明細行テンプレート
============================================================== --}}

    <template id="detail-template">

        <div
            class="detail-row border rounded-lg p-5 bg-gray-50"
            data-index="__INDEX__">

            {{-- 上段 --}}
            <div class="flex items-center justify-between mb-4">

                <div class="font-bold">
                    明細
                    <span class="detail-number"></span>
                </div>

                <button
                    type="button"
                    class="remove-detail text-red-600 border border-red-300 rounded px-3 py-1 hover:bg-red-50">
                    行削除
                </button>

            </div>


            {{-- 入力方式 --}}
            <div class="mb-4">

                <div class="flex gap-6">

                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            name="details[__INDEX__][input_mode]"
                            value="site"
                            class="detail-input-mode mr-2"
                            checked>
                        現場から選択
                    </label>

                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            name="details[__INDEX__][input_mode]"
                            value="manual"
                            class="detail-input-mode mr-2">
                        自由入力
                    </label>

                </div>

            </div>


            {{-- 現場選択 --}}
            <div class="detail-site-area">

                <label class="block font-medium mb-1">
                    現場
                </label>

                <select
                    class="detail-site-select border rounded w-full px-3 py-2">
                    <option value="">
                        元請と請求月を選択してください
                    </option>
                </select>

            </div>


            {{-- 自由入力 --}}
            <div class="detail-manual-area hidden">

                <label class="block font-medium mb-1">
                    内容
                </label>

                <input
                    type="text"
                    class="detail-manual-input border rounded w-full px-3 py-2"
                    placeholder="請求内容を入力してください">

            </div>


            {{-- 請負情報 --}}
            <div class="detail-contract-area hidden mt-5">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            契約金額
                        </label>

                        <div class="border rounded px-3 py-2 bg-gray-100">
                            <span class="detail-contract-amount-display">
                                0
                            </span>
                            円
                        </div>

                    </div>


                    <div>

                        <label class="block text-sm font-medium mb-1">
                            出来高％
                        </label>

                        <input
                            type="number"
                            name="details[__INDEX__][progress_rate]"
                            class="detail-progress-rate border rounded w-full px-3 py-2"
                            min="0"
                            max="100"
                            step="0.01"
                            value="">

                    </div>


                    <div>

                        <label class="block text-sm font-medium mb-1">
                            残％
                        </label>

                        <input
                            type="number"
                            name="details[__INDEX__][remaining_rate]"
                            class="detail-remaining-rate border rounded w-full px-3 py-2"
                            min="0"
                            max="100"
                            step="0.01"
                            value="">

                    </div>

                </div>

            </div>


            {{-- 明細 --}}
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-5">

                {{-- 内容 --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-medium mb-1">
                        請求内容
                    </label>

                    <input
                        type="text"
                        name="details[__INDEX__][description]"
                        class="detail-description border rounded w-full px-3 py-2"
                        placeholder="請求内容">

                </div>


                {{-- 数量 --}}
                <div>

                    <label class="block text-sm font-medium mb-1">
                        数量
                    </label>

                    <input
                        type="number"
                        name="details[__INDEX__][quantity]"
                        class="detail-quantity border rounded w-full px-3 py-2"
                        value="1"
                        min="0"
                        step="0.01">

                </div>


                {{-- 単位 --}}
                <div>

                    <label class="block text-sm font-medium mb-1">
                        単位
                    </label>

                    <input
                        type="text"
                        name="details[__INDEX__][unit]"
                        class="detail-unit border rounded w-full px-3 py-2"
                        value="式">

                </div>


                {{-- 税率 --}}
                <div>

                    <label class="block text-sm font-medium mb-1">
                        税率
                    </label>

                    <select
                        name="details[__INDEX__][tax_type]"
                        class="detail-tax-type border rounded w-full px-3 py-2">
                        <option value="taxable">
                            10%
                        </option>

                        <option value="exempt">
                            非課税
                        </option>
                    </select>

                </div>


                {{-- 単価 --}}
                <div>

                    <label class="block text-sm font-medium mb-1">
                        単価
                    </label>

                    <input
                        type="number"
                        name="details[__INDEX__][unit_price]"
                        class="detail-unit-price border rounded w-full px-3 py-2"
                        value="0"
                        min="0"
                        step="1">

                </div>

            </div>


            {{-- 金額 --}}
            <div class="mt-5 flex justify-end">

                <div class="text-lg font-bold">

                    金額：

                    <span class="detail-amount">
                        0
                    </span>

                    円

                </div>

            </div>


            {{-- hidden --}}
            <input
                type="hidden"
                name="details[__INDEX__][source_type]"
                class="detail-source-type"
                value="site">

            <input
                type="hidden"
                name="details[__INDEX__][site_id]"
                class="detail-site-hidden"
                value="">

            <input
                type="hidden"
                name="details[__INDEX__][work_type_id]"
                class="detail-work-type-id"
                value="">

        </div>

    </template>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const clientSelect =
                document.getElementById('client_id');

            const monthInput =
                document.getElementById('month');

            const detailList =
                document.getElementById('detail-list');

            const detailTemplate =
                document.getElementById('detail-template');

            const addDetailButton =
                document.getElementById('add-detail');

            const coverLetterArea =
                document.getElementById('cover-letter-area');

            const coverLetterList =
                document.getElementById('cover-letter-list');

            const addCoverLetterButton =
                document.getElementById('add-cover-letter');


            let detailIndex = 0;


            /*
            |--------------------------------------------------------------------------
            | 数値フォーマット
            |--------------------------------------------------------------------------
            */

            function formatNumber(value) {

                return Number(value || 0)
                    .toLocaleString('ja-JP');

            }


            /*
            |--------------------------------------------------------------------------
            | 送付状
            |--------------------------------------------------------------------------
            */

            function updateCoverLetterArea() {

                const selected =
                    document.querySelector(
                        'input[name="send_cover_letter"]:checked'
                    );

                if (
                    selected &&
                    selected.value === 'yes'
                ) {
                    coverLetterArea.classList.remove('hidden');
                } else {
                    coverLetterArea.classList.add('hidden');
                }
            }


            document
                .querySelectorAll(
                    'input[name="send_cover_letter"]'
                )
                .forEach(function(radio) {

                    radio.addEventListener(
                        'change',
                        updateCoverLetterArea
                    );

                });


            addCoverLetterButton.addEventListener(
                'click',
                function() {

                    const row =
                        document.createElement('div');

                    row.className =
                        'flex gap-2 cover-letter-row';

                    row.innerHTML = `
                    <input
                        type="text"
                        name="cover_letters[]"
                        placeholder="書類名を入力してください"
                        class="border rounded px-3 py-2 flex-1"
                    >

                    <button
                        type="button"
                        class="remove-cover-letter border rounded px-3 py-2 text-red-600 hover:bg-red-50"
                    >
                        削除
                    </button>
                `;

                    coverLetterList.appendChild(row);
                }
            );


            coverLetterList.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target.classList.contains(
                            'remove-cover-letter'
                        )
                    ) {

                        const rows =
                            coverLetterList.querySelectorAll(
                                '.cover-letter-row'
                            );

                        if (rows.length > 1) {
                            event.target
                                .closest('.cover-letter-row')
                                .remove();
                        }

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | 現場一覧取得
            |--------------------------------------------------------------------------
            */

            async function loadSites() {

                const clientId =
                    clientSelect.value;

                const month =
                    monthInput.value;

                const rows =
                    detailList.querySelectorAll(
                        '.detail-row'
                    );

                if (!clientId || !month) {

                    rows.forEach(function(row) {

                        const select =
                            row.querySelector(
                                '.detail-site-select'
                            );

                        select.innerHTML = `
                        <option value="">
                            元請と請求月を選択してください
                        </option>
                    `;

                    });

                    return;
                }


                try {

                    const response =
                        await fetch(
                            `{{ route('invoices.sites') }}?client_id=${encodeURIComponent(clientId)}&month=${encodeURIComponent(month)}`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );


                    if (!response.ok) {
                        throw new Error(
                            '現場一覧の取得に失敗しました。'
                        );
                    }


                    const data =
                        await response.json();

                    const sites =
                        data.sites ?? [];


                    rows.forEach(function(row) {

                        const select =
                            row.querySelector(
                                '.detail-site-select'
                            );

                        const currentValue =
                            select.value;

                        select.innerHTML = `
                        <option value="">
                            現場を選択してください
                        </option>
                    `;


                        sites.forEach(function(site) {

                            const option =
                                document.createElement(
                                    'option'
                                );

                            option.value =
                                site.id;

                            option.textContent =
                                `${site.name}（${site.contract_type}）`;

                            option.dataset.site =
                                JSON.stringify(site);

                            select.appendChild(option);

                        });


                        if (currentValue) {
                            select.value = currentValue;
                        }

                    });

                } catch (error) {

                    console.error(error);

                    alert(
                        '現場一覧の取得に失敗しました。'
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | 現場詳細取得
            |--------------------------------------------------------------------------
            */

            async function loadSiteDetails(row, siteId) {

                const month = monthInput.value;

                if (!siteId || !month) {
                    return;
                }

                try {

                    const response = await fetch(
                        `{{ route('invoices.site-details') }}?site_id=${encodeURIComponent(siteId)}&month=${encodeURIComponent(month)}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            '現場情報の取得に失敗しました。'
                        );
                    }

                    const data = await response.json();

                    row.dataset.siteData =
                        JSON.stringify(data);

                    const site = data.site;

                    /*
                    |--------------------------------------------------------------------------
                    | 請負
                    |--------------------------------------------------------------------------
                    */

                    if (site.contract_type === '請負') {

                        const contractArea =
                            row.querySelector(
                                '.detail-contract-area'
                            );

                        const contractAmountDisplay =
                            row.querySelector(
                                '.detail-contract-amount-display'
                            );

                        const unitPrice =
                            row.querySelector(
                                '.detail-unit-price'
                            );

                        const description =
                            row.querySelector(
                                '.detail-description'
                            );

                        const quantity =
                            row.querySelector(
                                '.detail-quantity'
                            );

                        const unit =
                            row.querySelector(
                                '.detail-unit'
                            );

                        contractArea.classList.remove(
                            'hidden'
                        );

                        contractAmountDisplay.textContent =
                            formatNumber(
                                site.contract_amount
                            );

                        unitPrice.value =
                            Math.round(
                                Number(site.contract_amount) || 0
                            );

                        quantity.value = 1;

                        unit.value = '式';

                        description.value =
                            `${site.name} 解体工事`;

                        row.querySelector(
                            '.detail-source-type'
                        ).value = 'site';

                        row.querySelector(
                            '.detail-site-hidden'
                        ).value = site.id;

                        recalculateRow(row);
                        recalculateTotals();

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | 常用
                    |--------------------------------------------------------------------------
                    */

                    /*
                    | 常用の場合は、元の選択行を削除して、
                    | 日報の内容から明細を作り直す
                    */

                    const rowIndex =
                        Number(row.dataset.index);


                    /*
                    | 元の行を削除
                    */

                    row.remove();


                    /*
                    | 作業内容
                    */

                    const workTypes =
                        data.work_types ?? [];


                    workTypes.forEach(function(workType) {

                        const sales =
                            Number(workType.sales) || 0;

                        /*
                        | 売上0の作業は請求明細にしない
                        */

                        if (sales <= 0) {
                            return;
                        }


                        const newRow =
                            addDetail(false);


                        const siteSelect =
                            newRow.querySelector(
                                '.detail-site-select'
                            );

                        siteSelect.value =
                            site.id;


                        newRow.querySelector(
                                '.detail-site-hidden'
                            ).value =
                            site.id;


                        /*
                        | 作業内容
                        */

                        let workName =
                            '解体工事';


                        if (
                            workType.name === '石綿'
                        ) {
                            workName =
                                'アスベスト除去工事';
                        }


                        newRow.querySelector(
                                '.detail-description'
                            ).value =
                            `${site.name} ${workName}`;


                        /*
                        | 数量
                        */

                        newRow.querySelector(
                            '.detail-quantity'
                        ).value = 1;


                        /*
                        | 単位
                        */

                        newRow.querySelector(
                            '.detail-unit'
                        ).value = '式';


                        /*
                        | 単価
                        */

                        newRow.querySelector(
                                '.detail-unit-price'
                            ).value =
                            Math.round(sales);


                        /*
                        | 作業種別
                        */

                        newRow.querySelector(
                                '.detail-work-type-id'
                            ).value =
                            workType.id;


                        /*
                        | source
                        */

                        newRow.querySelector(
                                '.detail-source-type'
                            ).value =
                            'site';


                        /*
                        | 請負情報は非表示
                        */

                        newRow.querySelector(
                            '.detail-contract-area'
                        ).classList.add(
                            'hidden'
                        );


                        recalculateRow(newRow);

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | 交通費
                    |--------------------------------------------------------------------------
                    */

                    const transportation =
                        Number(data.transportation) || 0;


                    if (transportation > 0) {

                        const newRow =
                            addDetail(false);


                        setupAutomaticCostRow(
                            newRow,
                            site,
                            'transportation',
                            '交通費',
                            transportation
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | 高速代
                    |--------------------------------------------------------------------------
                    */

                    const expressway =
                        Number(data.expressway) || 0;


                    if (expressway > 0) {

                        const newRow =
                            addDetail(false);


                        setupAutomaticCostRow(
                            newRow,
                            site,
                            'expressway',
                            '高速代',
                            expressway
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | 駐車場代
                    |--------------------------------------------------------------------------
                    */

                    const parking =
                        Number(data.parking) || 0;


                    if (parking > 0) {

                        const newRow =
                            addDetail(false);


                        setupAutomaticCostRow(
                            newRow,
                            site,
                            'parking',
                            '駐車場代',
                            parking
                        );
                    }


                    updateDetailNumber();

                    recalculateTotals();

                } catch (error) {

                    console.error(error);

                    alert(
                        '現場情報の取得に失敗しました。'
                    );
                }

            }

            /*
            自動生成された費用明細
            --------------------------------------------------------------------------
            */

            function setupAutomaticCostRow(
                row,
                site,
                sourceType,
                costName,
                amount
            ) {

                row.querySelector(
                        '.detail-site-select'
                    ).value =
                    site.id;


                row.querySelector(
                        '.detail-site-hidden'
                    ).value =
                    site.id;


                row.querySelector(
                        '.detail-description'
                    ).value =
                    `${site.name} ${costName}`;


                row.querySelector(
                    '.detail-quantity'
                ).value = 1;


                row.querySelector(
                    '.detail-unit'
                ).value = '式';


                row.querySelector(
                        '.detail-unit-price'
                    ).value =
                    Math.round(amount);


                row.querySelector(
                        '.detail-source-type'
                    ).value =
                    sourceType;


                row.querySelector(
                    '.detail-contract-area'
                ).classList.add(
                    'hidden'
                );


                recalculateRow(row);

            }


            /*
            |--------------------------------------------------------------------------
            | 明細行追加
            |--------------------------------------------------------------------------
            */

            function addDetail(loadSiteList = true) {

                const index =
                    detailIndex++;

                const html =
                    detailTemplate.innerHTML
                    .replaceAll(
                        '__INDEX__',
                        index
                    );

                detailList.insertAdjacentHTML(
                    'beforeend',
                    html
                );

                const row =
                    detailList.lastElementChild;

                updateDetailNumber();

                setupDetailEvents(row);

                if (loadSiteList) {
                    loadSites();
                }

                recalculateTotals();

                return row;

            }


            /*
            |--------------------------------------------------------------------------
            | 明細番号
            |--------------------------------------------------------------------------
            */

            function updateDetailNumber() {

                detailList
                    .querySelectorAll('.detail-row')
                    .forEach(function(row, index) {

                        const number =
                            row.querySelector(
                                '.detail-number'
                            );

                        number.textContent =
                            index + 1;

                    });

            }


            /*
            |--------------------------------------------------------------------------
            | 明細イベント
            |--------------------------------------------------------------------------
            */

            function setupDetailEvents(row) {

                /*
                |--------------------------------------------------------------
                | 入力方式
                |--------------------------------------------------------------
                */

                row.querySelectorAll(
                    '.detail-input-mode'
                ).forEach(function(radio) {

                    radio.addEventListener(
                        'change',
                        function() {

                            const siteArea =
                                row.querySelector(
                                    '.detail-site-area'
                                );

                            const manualArea =
                                row.querySelector(
                                    '.detail-manual-area'
                                );

                            const siteSelect =
                                row.querySelector(
                                    '.detail-site-select'
                                );

                            const manualInput =
                                row.querySelector(
                                    '.detail-manual-input'
                                );

                            const sourceType =
                                row.querySelector(
                                    '.detail-source-type'
                                );


                            if (
                                this.value === 'site'
                            ) {

                                siteArea.classList.remove(
                                    'hidden'
                                );

                                manualArea.classList.add(
                                    'hidden'
                                );

                                siteSelect.disabled =
                                    false;

                                sourceType.value =
                                    'site';

                            } else {

                                siteArea.classList.add(
                                    'hidden'
                                );

                                manualArea.classList.remove(
                                    'hidden'
                                );

                                siteSelect.disabled =
                                    true;

                                sourceType.value =
                                    'manual';


                                const description =
                                    row.querySelector(
                                        '.detail-description'
                                    );

                                description.value =
                                    manualInput.value;

                            }

                        }
                    );

                });


                /*
                |--------------------------------------------------------------
                | 自由入力
                |--------------------------------------------------------------
                */

                const manualInput =
                    row.querySelector(
                        '.detail-manual-input'
                    );


                manualInput.addEventListener(
                    'input',
                    function() {

                        const mode =
                            row.querySelector(
                                '.detail-input-mode:checked'
                            );


                        if (
                            mode &&
                            mode.value === 'manual'
                        ) {

                            row.querySelector(
                                    '.detail-description'
                                ).value =
                                this.value;

                        }

                    }
                );


                /*
                |--------------------------------------------------------------
                | 現場選択
                |--------------------------------------------------------------
                */

                const siteSelect =
                    row.querySelector(
                        '.detail-site-select'
                    );


                siteSelect.addEventListener(
                    'change',
                    async function() {

                        const siteId =
                            this.value;


                        row.querySelector(
                                '.detail-site-hidden'
                            ).value =
                            siteId;


                        if (!siteId) {

                            row.querySelector(
                                '.detail-contract-area'
                            ).classList.add(
                                'hidden'
                            );

                            row.querySelector(
                                '.detail-description'
                            ).value = '';

                            row.querySelector(
                                '.detail-unit-price'
                            ).value = 0;

                            recalculateRow(row);
                            recalculateTotals();

                            return;
                        }


                        await loadSiteDetails(
                            row,
                            siteId
                        );

                    }
                );


                /*
                |--------------------------------------------------------------
                | 数量・単価・税率
                |--------------------------------------------------------------
                */

                row.querySelectorAll(
                    '.detail-quantity, .detail-unit-price, .detail-tax-type'
                ).forEach(function(input) {

                    input.addEventListener(
                        'input',
                        function() {

                            recalculateRow(row);
                            recalculateTotals();

                        }
                    );

                    input.addEventListener(
                        'change',
                        function() {

                            recalculateRow(row);
                            recalculateTotals();

                        }
                    );

                });


                /*
                |--------------------------------------------------------------
                | 出来高
                |--------------------------------------------------------------
                */

                const progressRate =
                    row.querySelector(
                        '.detail-progress-rate'
                    );

                const remainingRate =
                    row.querySelector(
                        '.detail-remaining-rate'
                    );


                progressRate.addEventListener(
                    'input',
                    function() {

                        const value =
                            Number(this.value);

                        if (
                            Number.isFinite(value)
                        ) {

                            remainingRate.value =
                                Math.max(
                                    0,
                                    100 - value
                                ).toFixed(2);

                        }


                        recalculateRow(row);
                        recalculateTotals();

                    }
                );


                remainingRate.addEventListener(
                    'input',
                    function() {

                        const value =
                            Number(this.value);

                        if (
                            Number.isFinite(value)
                        ) {

                            progressRate.value =
                                Math.max(
                                    0,
                                    100 - value
                                ).toFixed(2);

                        }


                        recalculateRow(row);
                        recalculateTotals();

                    }
                );


                /*
                |--------------------------------------------------------------
                | 削除
                |--------------------------------------------------------------
                */

                row.querySelector(
                    '.remove-detail'
                ).addEventListener(
                    'click',
                    function() {

                        const rows =
                            detailList.querySelectorAll(
                                '.detail-row'
                            );


                        if (rows.length <= 1) {

                            alert(
                                '請求内訳は1行以上必要です。'
                            );

                            return;
                        }


                        row.remove();

                        updateDetailNumber();

                        recalculateTotals();

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | 明細金額計算
            |--------------------------------------------------------------------------
            */

            function recalculateRow(row) {

                const quantity =
                    Number(
                        row.querySelector(
                            '.detail-quantity'
                        ).value
                    ) || 0;


                const unitPrice =
                    Number(
                        row.querySelector(
                            '.detail-unit-price'
                        ).value
                    ) || 0;


                const amount =
                    Math.round(
                        quantity * unitPrice
                    );


                row.querySelector(
                        '.detail-amount'
                    ).textContent =
                    formatNumber(amount);

            }


            /*
            |--------------------------------------------------------------------------
            | 合計計算
            |--------------------------------------------------------------------------
            */

            function recalculateTotals() {

                let taxableSubtotal = 0;
                let exemptSubtotal = 0;


                detailList
                    .querySelectorAll('.detail-row')
                    .forEach(function(row) {

                        const quantity =
                            Number(
                                row.querySelector(
                                    '.detail-quantity'
                                ).value
                            ) || 0;


                        const unitPrice =
                            Number(
                                row.querySelector(
                                    '.detail-unit-price'
                                ).value
                            ) || 0;


                        const amount =
                            Math.round(
                                quantity * unitPrice
                            );


                        const taxType =
                            row.querySelector(
                                '.detail-tax-type'
                            ).value;


                        if (
                            taxType === 'taxable'
                        ) {

                            taxableSubtotal += amount;

                        } else {

                            exemptSubtotal += amount;

                        }

                    });


                const tax =
                    Math.floor(
                        taxableSubtotal * 0.10
                    );


                const total =
                    taxableSubtotal +
                    exemptSubtotal +
                    tax;


                document.getElementById(
                        'taxable-subtotal'
                    ).textContent =
                    formatNumber(
                        taxableSubtotal
                    );


                document.getElementById(
                        'exempt-subtotal'
                    ).textContent =
                    formatNumber(
                        exemptSubtotal
                    );


                document.getElementById(
                        'tax-total'
                    ).textContent =
                    formatNumber(
                        tax
                    );


                document.getElementById(
                        'grand-total'
                    ).textContent =
                    formatNumber(
                        total
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | 元請・請求月変更
            |--------------------------------------------------------------------------
            */

            clientSelect.addEventListener(
                'change',
                loadSites
            );

            monthInput.addEventListener(
                'change',
                loadSites
            );


            /*
            |--------------------------------------------------------------------------
            | 行追加
            |--------------------------------------------------------------------------
            */

            addDetailButton.addEventListener(
                'click',
                addDetail
            );


            /*
            |--------------------------------------------------------------------------
            | 初期状態
            |--------------------------------------------------------------------------
            */

            updateCoverLetterArea();

            addDetail();

        });
    </script>
    ```

</x-app-layout>