<x-app-layout>
    <div x-data="documentGenerator()" class="flex h-full p-3 gap-3">
        <div class="w-1/3 flex flex-col gap-3 border rounded-lg p-3">
            <label for="internal">
                <input id="internal" type="checkbox" x-model="internal">
                <span>مذكرة داخلية</span>
            </label>
            <input type="text" placeholder="From" x-model="from" class="border p-2 rounded">
            <div class=" flex w-full gap-1">
                <select x-model="prefix">
                    <option value="السيد">السيد</option>
                    <option value="السادة">السادة</option>
                </select>
                <input type="text" placeholder="To" x-model="to" class="border p-2 rounded flex-1">
            </div>
            <label for="official">
                <input id="official" type="checkbox" x-model="official">
                <span>بالطريق الرسمي</span>
            </label>
            <textarea placeholder="العنوان" x-model="address" rows="3"></textarea>
            <textarea placeholder="subject" x-model="subject" rows="5" class="border p-2 rounded"></textarea>
            <textarea placeholder="Body" id="body" x-model="body" rows="10" class="border p-2 rounded"></textarea>
            <label for="code">
                <input id="code" type="checkbox" x-model="code">
                <span>كود ترميز القطاع</span>
            </label>
            <label for="has_attachments">
                <input id="has_attachments" type="checkbox" x-model="has_attachments">
                <span>مرفقات</span>
            </label>
            <textarea placeholder="Copy to" x-model="copyTo" rows="5" class="border p-2 rounded"></textarea>
            <label for="cover">
                <input id="cover" type="checkbox" x-model="cover">
                <span>اصل</span>
            </label>
        </div>










        <div class="w-2/3 border rounded-lg p-3 overflow-auto">
            {{-- <div class="mt-3 border rounded overflow-auto" style="height: calc(100% - 60px);"> --}}
            <div id="previewContent" class=" relative flex flex-col justify-between mx-auto bg-white px-16 pt-48 pb-28"
                x-bind:style="cover ?
                    'max-width: 210mm; min-height: 297mm; background-image: url({{ asset('images/cover.jpeg') }}); background-size: cover; background-repeat: no-repeat;' :
                    'max-width: 210mm; min-height: 297mm;'">

                <div>
                    <p x-show="internal" class="text-center text-2xl font-bold mb-2 font-sultann">مذكرة داخلية</p>

                    <div x-show="to">
                        <div class=" flex gap-2 font-sultanb text-2xl">
                            <p x-text='prefix'></p>
                            <p>/</p>
                            <p x-text='to'></p>
                            <p x-text='postfix' class="ms-10"></p>
                        </div>
                        <p x-html="address" class=" whitespace-pre-wrap"></p>
                        <p x-show="official" class="font-sultanb text-xl">بالطريق الرسمي</p>
                        <p class="font-sultanb text-xl">تحية طيبة وبعد،،،</p>
                    </div>

                    <p x-show="subject" x-html="subject"
                        class="mt-5 w-3/4 mx-auto text-center whitespace-pre-wrap font-sultanb font-bold text-xl border-b border-black">
                    </p>

                    <pre class="mt-5 whitespace-break-spaces text-justify  font-sultann text-lg leading-relaxed" x-html="body"></pre>

                    <p x-show="body" class="mt-5 font-sultanb text-xl text-center">وتفضلوا بقبول وافر الاحترام والتقدير
                    </p>

                    <p class="font-sultanb text-2xl w-2/6 text-center ms-auto mt-5 " x-text='from'></p>

                </div>


                <div {{-- class="absolute bottom-28 start-16" --}}>
                    <p x-show="code" class="font-sultann text-sm">كود ترميز القطاع (15000)</p>
                    <p x-show="has_attachments" class="font-sultann text-sm">المرفقات: كما ورد اعلاه</p>
                    <p x-show="copyTo && !cover" class="font-sultann text-sm">نسخة لكل من:</p>
                    <pre x-show="!cover" class="whitespace-pre-wrap ms-3 text-sm font-sultann" x-text="copyTo"></pre>
                </div>

            </div>
            {{-- </div> --}}
        </div>
    </div>

    <style>
        @media print {

            /* Hide all elements except the previewContent */

            @page {
                size: A4;
                margin: 20mm !important;
                /* Force the browser to respect the margin */
                /* Adjust margins as needed */
            }

            #previewContent {
                display: block;
                position: absolute;
                left: 0;
                top: 0;
                /* margin-bottom: 112px; */
                width: 210mm;
                /* A4 width */
                height: 297mm;
                /* A4 height */
                background-color: white;
                /* Ensure white background for printing */
                page-break-inside: avoid;
                /* Prevent content from being split */
            }
        }
    </style>


    <script>
        function documentGenerator() {
            return {
                cover: false,
                code: false,
                internal: false,
                official: false,
                has_attachments: true,
                address: '',
                prefix: 'السيد',
                from: '',
                to: '',
                subject: '',
                body:'',
                copyTo:'',


                get postfix() {
                    switch (this.prefix) {
                        case 'السيد':
                            return 'المحترم'
                            break
                        case 'السادة':
                            return 'المحترمين'
                            break
                    }
                },

                printPreview() {
                    // Get the preview content element
                    const printContent = document.getElementById('previewContent').outerHTML;

                    // Create a new window
                    const printWindow = window.open('', '', 'width=800,height=600');
                }

            };
        }
    </script>
</x-app-layout>
