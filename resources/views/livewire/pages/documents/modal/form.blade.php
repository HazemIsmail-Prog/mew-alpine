        {{-- Document Form --}}
        <div class="flex flex-col p-3 gap-3 border border-primary border-dashed rounded-lg">
            <h1 class="text-primary font-extrabold">{{ __('Document Details') }}</h1>

            <x-test label="{{ __('Follwers') }}" :list="$this->users" model="documentForm.follow_ids" multiple />
            <x-test label="{{ __('Tags') }}" :list="$this->tags" model="documentForm.tag_ids" multiple />
            <x-test label="{{ __('Contract') }}" :list="$this->contracts" model="documentForm.contract_id" />

            <template x-if="documentForm.type == 'incoming'">
                <x-test label="{{ __('Sender') }}" :list="$this->stakeholders" model="documentForm.from_id" />
            </template>

            <template x-if="documentForm.type == 'outgoing'">
                <x-test label="{{ __('Receiver') }}" :list="$this->stakeholders" model="documentForm.to_id" />
            </template>

            <div>
                <x-input-label for="title" :value="__('Title')" />
                <textarea x-on:keydown.ctrl.enter.prevent="submitDocument" id="title"
                    class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none" x-model="documentForm.title"
                    rows="3"></textarea>
                <x-input-error :messages="$errors->get('documentForm.title')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="contents" :value="__('Contents')" />
                <textarea x-on:keydown.ctrl.enter.prevent="submitDocument" id="contents"
                    class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none" x-model="documentForm.content"
                    rows="2"></textarea>
                <x-input-error :messages="$errors->get('documentForm.content')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="notes" :value="__('Notes')" />
                <textarea x-on:keydown.ctrl.enter.prevent="submitDocument" id="notes"
                    class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none" x-model="documentForm.notes"
                    rows="2"></textarea>
                <x-input-error :messages="$errors->get('documentForm.notes')" class="mt-2" />
            </div>

            <div class=" flex-1"></div>

        </div>
