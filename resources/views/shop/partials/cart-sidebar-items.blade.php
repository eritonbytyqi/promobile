@if(count($cart) > 0)
    <div class="cs-items" id="csItems">
        @foreach($cart as $key => $item)
            <div class="cs-item" id="csItem-{{ $key }}">
                <div class="cs-item-img">
                    @if(!empty($item['image']))
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}">
                    @else
                        <div class="cs-item-img-ph">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    @endif
                </div>

                <div class="cs-item-info">
                    <div class="cs-item-name">{{ $item['name'] }}</div>

                    @if(!empty($item['color']) || !empty($item['storage']))
                        <div class="cs-item-meta" style="font-size:12px;color:#6e6e73;margin-top:2px;">
                            {{ $item['color'] ?? '' }}{{ !empty($item['color']) && !empty($item['storage']) ? ' • ' : '' }}{{ $item['storage'] ?? '' }}
                        </div>
                    @endif

                    <div class="cs-item-price">{{ number_format($item['price'], 2) }} €</div>

                    <div class="cs-item-qty">
                        <button class="cs-qty-btn" onclick="updateQty('{{ $key }}', {{ max(1, $item['quantity'] - 1) }})">
                            <i class="fa-solid fa-minus"></i>
                        </button>

                        <span class="cs-qty-num" id="csQty-{{ $key }}">{{ $item['quantity'] }}</span>

                        <button class="cs-qty-btn" onclick="updateQty('{{ $key }}', {{ $item['quantity'] + 1 }})">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>

                <button class="cs-item-remove" onclick="removeItem('{{ $key }}')">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        @endforeach
    </div>
@else
    <div class="cs-empty" id="csEmpty">
        <div class="cs-empty-icon"><i class="fa-solid fa-bag-shopping"></i></div>
        <div class="cs-empty-title">Shporta është bosh</div>
        <div class="cs-empty-sub">Shto produkte për të filluar blerjen</div>
        <a href="{{ url('/shop') }}" class="cs-empty-btn" onclick="closeCart()">
            Shko te produktet
        </a>
    </div>
@endif