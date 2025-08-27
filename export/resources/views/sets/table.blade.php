<div class="overflow-x-auto not-prose">
    <table class="min-w-full divide-y divide-gray-300 text-base">
        @foreach ($table as $row)
            @if ($loop->first && $first_row_headers)
                <thead>
                    <tr>
                        @foreach ($row['cells'] as $cell)
                            <th scope="col" @class([
                                'py-3 text-left font-semibold text-neutral-900 px-3 sm:px-6 xl:px-8',
                                'pl-0 sm:pl-0 xl:pl-0' => $loop->first,
                                'pr-0 sm:pr-0 xl:pr-0' => $loop->last,
                            ])>{{ $cell }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
            @endif

            @if (!$loop->first && $first_row_headers || !$first_row_headers)
                @if ($loop->first)
                    <tbody class="divide-y divide-gray-200">
                @endif

                <tr>
                    @foreach ($row['cells'] as $cell)
                        @if ($loop->parent->first && $first_column_headers)
                            <th class="py-3 text-left text-primary">{{ $cell }}</th>
                        @else
                            <td @class([
                                'py-3 px-3 sm:px-6 xl:px-8',
                                'pl-0 sm:pl-0 xl:pl-0' => $loop->first,
                                'pr-0 sm:pr-0 xl:pr-0' => $loop->last,
                            ])>{{ $cell }}</td>
                        @endif
                    @endforeach
                </tr>

                @if ($loop->last)
                    </tbody>
                @endif
            @endif
        @endforeach
    </table>

    @isset($caption)
        <span class="text-xs block pt-3 border-t border-neutral-300 text-muted-foreground">
            {!! $caption !!}
        </span>
    @endisset
</div>
