@if (!empty($alerts))
    <div class="alerts text-left">
        <!-- Validation Errors -->
        @foreach($alerts as $type => $messages)
            <div class="alert alert-{{$type}} alert-dismissable">
                @if (is_array($messages))
                    <ul>
                        @foreach($messages as $m)
                            <li>{{ $m }}</li>
                        @endforeach
                    </ul>
                @else
                    {{ $messages }}
                @endif
            </div>
        @endforeach
    </div>
@endif