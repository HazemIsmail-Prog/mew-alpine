<!DOCTYPE html>
<html lang="en" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Letter No. {{ $letter->id }}</title>
    <style>
        @font-face {
            font-family: 'Sultan Normal';
            src: url('fonts/SultanNormal.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Sultan Bold';
            src: url('fonts/SultanBold.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        .font-sultanb {
            font-family: "Sultan bold", sans-serif;
        }

        .font-sultann {
            font-family: "Sultan Normal", sans-serif;
        }

        @page {
            size: A4 portrait;
            margin: 7rem 0;
        }

        @page:first {
            margin: 0;
            margin-bottom: 7rem;
            background-image: url({{ $cover }});
            background-size: 100%;
            background-repeat: no-repeat;
        }

        /*  */
    </style>

</head>

<body class=" flex flex-col min-h-dvh w-[800px] mx-auto">

    <div class="mt-48"></div>

    <div class="content-wrapper justify-between mx-20">
        @if ($letter->internal)
            <p class="text-center text-2xl font-bold mb-2 font-sultann">مذكرة داخلية</p>
        @endif

        <div class="flex gap-2 font-sultanb text-2xl">
            <p>{{ $letter->prefix }}</p>
            <p>/</p>
            <p>{{ $letter->receiver }}</p>
            <p class="ms-auto me-10">{{ $letter->suffix }}</p>
        </div>
        @if ($letter->address)
            <p class="font-sultann text-xl">{!! $letter->address !!}</p>
        @endif

        @if ($letter->official)
            <p class="font-sultanb text-xl">بالطريق الرسمي</p>
        @endif

        <p class="font-sultanb text-xl">تحية طيبة وبعد،،،</p>

        <div
            class="mt-5 pb-3 w-fit max-w-[75%] mx-auto text-center font-sultanb font-bold text-xl border-b border-black">
            <span class="whitespace-pre-line">{!! $letter->subject !!}</span>
        </div>

        <div class="mt-5 text-justify  font-sultann text-lg leading-10" style="text-indent: 3rem;">
            <span class="whitespace-pre-line">{!! $letter->body !!}</span>
        </div>

        <div class=" mt-5 font-sultanb text-xl text-center">وتفضلوا بقبول وافر الاحترام والتقدير</div>

        <div class=" mt-5 pb-16 font-sultanb text-2xl w-2/6 text-center ms-auto">
            {!! $letter->sender !!}</div>

    </div>

    <div class="text-xs font-sultann mx-20 mt-auto">
        @if ($letter->code)
            <p>كود ترميز القطاع (15000)</p>
        @endif
        @if ($letter->has_attachments)
            <p>المرفقات: كما ورد أعلاه</p>
        @endif
        @if (!$cover)
            <p>نسخة لكل من:</p>
            <pre class=" ms-5 font-sultann">{!! $letter->copyTo !!}</pre>
        @endif
    </div>

</body>

<script>
    // window.print();
</script>

</html>
