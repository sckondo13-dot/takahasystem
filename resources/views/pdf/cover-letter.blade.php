 @extends('pdf.layout')

 @section('content')
 <style>
     @page {
         margin: 18mm 20mm;
     }

     .cover-letter {
         font-family: 'Noto Sans JP', sans-serif;
         font-size: 11pt;
         line-height: 1;
         color: #000;
     }

     .cover-date {
         text-align: right;
         margin-bottom: 24px;
     }

     .recipient {
         width: 60%;
         margin-bottom: 26px;
     }

     .recipient-name {
         display: inline-block;
         min-width: 250px;
         border-bottom: 1px solid #000;
         padding-bottom: 3px;
         font-size: 13pt;
     }

     .issuer {
         width: 20%;
         margin-left: auto;
         text-align: left;
         margin-bottom: 30px;
         line-height: 1;
         font-size: 10px;
     }

     .cover-title {
         text-align: center;
         font-size: 18pt;
         font-weight: bold;
         letter-spacing: 3px;
         margin: 28px 0 24px;
     }

     .greeting {
         margin-bottom: 10px;
     }

     .closing {
         text-align: right;
         margin-top: 8px;
     }

     .record-title {
         text-align: center;
         font-size: 14pt;
         margin: 24px 0 12px;
     }

     .document-heading {
         margin-bottom: 8px;
     }

     .document-list {
         margin-left: 28px;
     }

     .document-item {
         display: inline-block;
         min-width: 90%;
         border-bottom: 1px solid #000;
         padding-bottom: 2px;
         margin-bottom: 8px;
     }

     .end-mark {
         text-align: right;
         margin-top: 24px;
     }
 </style>

 <div class="cover-letter">

     {{-- 日付 --}}
     <div class="cover-date">
         {{ $invoice->invoice_date
            ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y年n月j日')
            : now()->format('Y年n月j日') }}
     </div>

     {{-- 宛先 --}}
     <div class="recipient">
         <div>{{ $invoice->client->address ?? '' }}</div>

         <div class="recipient-name">
             {{ $invoice->client->name ?? '' }}　様
         </div>
     </div>

     {{-- 差出人 --}}
     <div class="issuer">
         <div>{{ $invoice->company->name ?? '' }}</div>
         <div>〒{{ $invoice->company->postal_code ?? '' }}</div>
         <div>{{ $invoice->company->address ?? '' }}</div>
         <div>TEL：{{ $invoice->company->tel ?? '' }}</div>
         <div>担当：{{ $invoice->company->representative ?? '' }}</div>
     </div>

     {{-- タイトル --}}
     <div class="cover-title">
         書類送付のご案内
     </div>

     {{-- 挨拶文 --}}
     <div class="greeting">
         拝啓
     </div>

     <div>
         時下ますますご清祥のこととお慶び申し上げます。<br>
         日頃は格別のご愛顧を賜り厚く御礼申し上げます。<br>
         さて、下記書類を送付致しますので、ご査収くださいますようお願い申し上げます。
     </div>

     <div class="closing">
         敬具
     </div>

     {{-- 送付内容 --}}
     <div class="record-title">記</div>

     <div class="document-heading">
         送付内容
     </div>

     <div class="document-list">
         @forelse ($invoice->coverLetters as $coverLetter)
         <div class="document-item">
             {{ $coverLetter->name }}
         </div>
         @empty
         <div class="document-item">請求書</div>
         @endforelse
     </div>

     <div class="end-mark">以上</div>

 </div>
 @endsection