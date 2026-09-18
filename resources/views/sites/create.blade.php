<x-app-layout>

    <div class="max-w-6xl mx-auto py-8">

        <h1 class="text-2xl font-bold mb-6">
            請求書作成
        </h1>

        @if ($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form
            method="POST"
            action="{{ route('invoices.store') }}">

            @csrf

            {{-- ======================================== --}}
            {{-- 基本情報 --}}
            {{-- ======================================== --}}

            <div class="grid grid-cols-4 gap-5">

                {{-- 元請 --}}
                <div>

                    <label
                        for="client_id"
                        class="block mb-1">

                        元請

                    </label>

                    <select
                        id="client_id"
                        name="client_id"
                        class="border rounded w-full p-2">

                        <option value="">
                            選択してください
                        </option>

                        @foreach($clients as $client)

                        <option
                            value="{{ $client->id }}"
                            {{ old('client_id') == $client->id ? 'selected' : '' }}>

                            {{ $client->name }}

                        </option>

                        @endforeach

                    </select>

                    @error('client_id')
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                    @enderror

                </div>


                {{-- 請求月 --}}
                <div>

                    <label
                        for="month"
                        class="block mb-1">

                        請求月

                    </label>

                    <input
                        type="month"
                        id="month"
                        name="month"
                        value="{{ old('month', $invoiceMonth ?? now()->format('Y-m')) }}"
                        class="border rounded w-full p-2">

                    @error('month')
                    <p class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </p>
                    @enderror

                </div>


                {{-- 現場選択方法 --}}
                <div>

                    <label class="block mb-1">
                        現場
                    </label>

                    <div class="flex gap-4 mb-2">

                        <label class="flex items-center gap-1">

                            <input
                                type="radio"
                                name="site_mode"
                                value="select"
                                checked>

                            プルダウン

                        </label>

                        <label class="flex items-center gap-1">

                            <input
                                type="radio"
                                name="site_mode"
                                value="free">

                            自由入力

                        </label>

                    </div>

                </div>

            </div>


            {{-- 現場プルダウン --}}
            <div
                id="siteSelectArea"
                class="mt-4">

                <select
                    id="site_id"
                    name="site_id"
                    class="w-full border rounded p-2"
                    disabled>

                    <option value="">
                        先に元請けと請求月を選択してください
                    </option>

                </select>

            </div>


            {{-- 現場自由入力 --}}
            <div
                id="siteFreeArea"
                class="mt-4 hidden">

                <label
                    for="site_name"
                    class="block mb-1">

                    現場名

                </label>

                <input
                    type="text"
                    id="site_name"
                    name="site_name"
                    value="{{ old('site_name') }}"
                    class="w-full border rounded p-2"
                    placeholder="現場名を入力してください">

            </div>


            {{-- ======================================== --}}
            {{-- 請負現場情報 --}}
            {{-- ======================================== --}}

            <div
                id="contractSiteArea"
                class="hidden mt-6 bg-gray-50 border rounded p-5">

                <h2 class="font-bold mb-4">
                    請負現場情報
                </h2>

                <div class="grid grid-cols-3 gap-5">

                    <div>

                        <label class="block mb-1">
                            契約金額
                        </label>

                        <div
                            id="contractAmount"
                            class="border rounded bg-white p-2">

                            -

                        </div>

                    </div>


                    <div>

                        <label class="block mb-1">
                            現在の残率
                        </label>

                        <div
                            id="currentRemainingRate"
                            class="border rounded bg-white p-2">

                            -

                        </div>

                    </div>


                    <div>

                        <label class="block mb-1">
                            今回出来高率（％）
                        </label>

                        <input
                            type="number"
                            id="progressRate"
                            name="progress_rate"
                            value="{{ old('progress_rate') }}"
                            min="0"
                            max="100"
                            step="1"
                            class="w-full border rounded p-2"
                            placeholder="例：30">

                    </div>

                </div>

                <p class="text-sm text-gray-600 mt-3">
                    ※ 今回の請求に使用する出来高率を入力してください。
                </p>

            </div>


            <hr class="my-8">


            {{-- ======================================== --}}
            {{-- 請求日・支払期限など --}}
            {{-- ======================================== --}}

            <div class="grid grid-cols-2 gap-6">

                {{-- 請求日 --}}
                <div>

                    <label
                        for="invoice_date"
                        class="block mb-1">

                        請求日

                    </label>

                    <input
                        type="date"
                        id="invoice_date"
                        name="invoice_date"
                        value="{{ old('invoice_date', $invoiceDate ?? now()->format('Y-m-d')) }}"
                        class="border rounded w-full p-2">

                </div>


                {{-- 支払期限 --}}
                <div>

                    <label
                        for="payment_due"
                        class="block mb-1">

                        支払期限

                    </label>

                    <input
                        type="date"
                        id="payment_due"
                        name="payment_due"
                        value="{{ old('payment_due', $paymentDue ?? now()->addMonth()->endOfMonth()->format('Y-m-d')) }}"
                        class="border rounded w-full p-2">

                </div>


                {{-- 件名 --}}
                <div>

                    <label
                        for="title"
                        class="block mb-1">

                        件名

                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="○月分 解体工事"
                        class="border rounded w-full p-2">

                </div>


                {{-- 請求書番号 --}}
                <div>

                    <label
                        for="invoice_no"
                        class="block mb-1">

                        請求書番号

                    </label>

                    <input
                        type="text"
                        id="invoice_no"
                        name="invoice_no"
                        readonly
                        value="{{ $invoiceNo ?? '' }}"
                        class="border rounded w-full bg-gray-100 p-2">

                </div>

            </div>


            <hr class="my-8">


            {{-- ======================================== --}}
            {{-- 備考 --}}
            {{-- ======================================== --}}

            <div>

                <label
                    for="remarks"
                    class="block mb-1">

                    備考

                </label>

                <textarea
                    id="remarks"
                    name="remarks"
                    rows="3"
                    class="border rounded w-full p-2"
                    placeholder="備考があれば入力してください">{{ old('remarks') }}</textarea>

            </div>


            {{-- ======================================== --}}
            {{-- 登録 --}}
            {{-- ======================================== --}}

            <div class="mt-8">

                <button
                    type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">

                    請求書作成

                </button>

            </div>

        </form>

    </div>


    {{-- ======================================== --}}
    {{-- JavaScript --}}
    {{-- ======================================== --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const clientSelect = document.getElementById('client_id');
            const monthInput = document.getElementById('month');

            const siteSelect = document.getElementById('site_id');

            const siteSelectArea =
                document.getElementById('siteSelectArea');

            const siteFreeArea =
                document.getElementById('siteFreeArea');

            const siteNameInput =
                document.getElementById('site_name');

            const contractSiteArea =
                document.getElementById('contractSiteArea');

            const contractAmount =
                document.getElementById('contractAmount');

            const currentRemainingRate =
                document.getElementById('currentRemainingRate');

            const progressRate =
                document.getElementById('progressRate');


            let sites = [];


            /*
             * 元請・請求月変更
             */
            async function loadSites() {

                const clientId = clientSelect.value;
                const month = monthInput.value;

                siteSelect.innerHTML = '';

                contractSiteArea.classList.add('hidden');


                /*
                 * 元請または請求月が未選択
                 */
                if (!clientId || !month) {

                    siteSelect.disabled = true;

                    const option = document.createElement('option');

                    option.value = '';
                    option.textContent =
                        '先に元請けと請求月を選択してください';

                    siteSelect.appendChild(option);

                    sites = [];

                    return;
                }


                /*
                 * 読み込み中
                 */
                siteSelect.disabled = true;

                const loadingOption =
                    document.createElement('option');

                loadingOption.value = '';
                loadingOption.textContent =
                    '現場を取得しています...';

                siteSelect.appendChild(loadingOption);


                try {

                    const url =
                        `{{ route('invoices.sites') }}` +
                        `?client_id=${encodeURIComponent(clientId)}` +
                        `&month=${encodeURIComponent(month)}`;


                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });


                    if (!response.ok) {
                        throw new Error(
                            `現場の取得に失敗しました（HTTP ${response.status}）`
                        );
                    }


                    /*
                     * Laravelから返ってくるのは
                     *
                     * {
                     *     count: 9,
                     *     sites: [...]
                     * }
                     *
                     * なので sites プロパティを取得する
                     */
                    const data = await response.json();

                    sites = data.sites ?? [];


                    siteSelect.innerHTML = '';


                    /*
                     * 現場がない場合
                     */
                    if (!Array.isArray(sites) || sites.length === 0) {

                        const option =
                            document.createElement('option');

                        option.value = '';
                        option.textContent =
                            '対象月に進行中の現場はありません';

                        siteSelect.appendChild(option);

                        siteSelect.disabled = true;

                        return;
                    }


                    /*
                     * 初期選択
                     */
                    const defaultOption =
                        document.createElement('option');

                    defaultOption.value = '';
                    defaultOption.textContent =
                        '選択してください';

                    siteSelect.appendChild(defaultOption);


                    /*
                     * 現場一覧
                     */
                    sites.forEach(function(site) {

                        const option =
                            document.createElement('option');

                        option.value = site.id;

                        option.textContent =
                            `${site.name}（${site.contract_type}）`;

                        /*
                         * 後で利用する情報をdata属性にも保存
                         */
                        option.dataset.contractType =
                            site.contract_type ?? '';

                        option.dataset.contractAmount =
                            site.contract_amount ?? 0;

                        option.dataset.remainingRate =
                            site.remaining_rate ?? 100;

                        siteSelect.appendChild(option);

                    });


                    /*
                     * 現場を選択可能にする
                     */
                    siteSelect.disabled = false;


                } catch (error) {

                    console.error(
                        '現場取得エラー:',
                        error
                    );


                    sites = [];

                    siteSelect.innerHTML = '';

                    const option =
                        document.createElement('option');

                    option.value = '';
                    option.textContent =
                        '現場の取得に失敗しました';

                    siteSelect.appendChild(option);

                    siteSelect.disabled = true;

                }

            }


            /*
             * 現場選択
             */
            siteSelect.addEventListener(
                'change',
                function() {

                    const siteId = this.value;

                    contractSiteArea.classList.add('hidden');


                    if (!siteId) {
                        return;
                    }


                    const site =
                        sites.find(function(item) {

                            return String(item.id) ===
                                String(siteId);

                        });


                    if (!site) {
                        return;
                    }


                    /*
                     * 請負の場合
                     */
                    if (site.contract_type === '請負') {

                        contractSiteArea.classList.remove(
                            'hidden'
                        );


                        contractAmount.textContent =
                            Number(site.contract_amount || 0)
                            .toLocaleString('ja-JP') + '円';


                        currentRemainingRate.textContent =
                            Number(site.remaining_rate || 0)
                            .toLocaleString('ja-JP') + '%';

                    }

                }
            );


            /*
             * 元請変更
             */
            clientSelect.addEventListener(
                'change',
                loadSites
            );


            /*
             * 請求月変更
             */
            monthInput.addEventListener(
                'change',
                loadSites
            );


            /*
             * 自由入力 / プルダウン
             */
            const siteModeRadios =
                document.querySelectorAll(
                    'input[name="site_mode"]'
                );


            siteModeRadios.forEach(function(radio) {

                radio.addEventListener(
                    'change',
                    function() {

                        if (this.value === 'free') {

                            /*
                             * 自由入力
                             */
                            siteSelectArea.classList.add(
                                'hidden'
                            );

                            siteFreeArea.classList.remove(
                                'hidden'
                            );

                            siteSelect.disabled = true;

                            siteNameInput.disabled = false;

                            contractSiteArea.classList.add(
                                'hidden'
                            );

                        } else {

                            /*
                             * プルダウン
                             */
                            siteSelectArea.classList.remove(
                                'hidden'
                            );

                            siteFreeArea.classList.add(
                                'hidden'
                            );

                            siteNameInput.disabled = true;


                            /*
                             * 元請・請求月が選択済みなら
                             * 現場を再取得する
                             */
                            if (
                                clientSelect.value &&
                                monthInput.value
                            ) {
                                loadSites();
                            } else {
                                siteSelect.disabled = true;
                            }

                        }

                    }
                );

            });

        });
    </script>

</x-app-layout>