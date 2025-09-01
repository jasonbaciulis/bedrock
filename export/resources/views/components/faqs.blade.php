<dl {{ $attributes->except(['items']) }}>
    {{ $slot }}
</dl>

@section('json_ld')
    <script type="application/ld+json" id="schema-faq">
        @json($faqSchema(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    </script>
@endsection
