<section class="m-section">
    <div class="container">
        <x-section-header :title="$block->title" :text="$block->text ?? null" />

        <div class="mx-auto max-w-xl">
            <x-form
                :form="$block->form"
                :success_message="$block->success_message"
                :submit_label="$block->submit_label"
            />
        </div>
    </div>
</section>
