@php
    $thumbHeight = $height ?? 160;
    $scale = 0.28;
    $frameWidth = round(100 / $scale, 2);
    $frameHeight = round($thumbHeight / $scale);
@endphp
<div style="width:100%;height:{{ $thumbHeight }}px;overflow:hidden;background:#f1f5f9;border-radius:10px 10px 0 0;position:relative;border-bottom:1px solid #e2e8f0;">
    <iframe
        srcdoc="{{ $template->body }}"
        loading="lazy"
        tabindex="-1"
        style="width:{{ $frameWidth }}%;height:{{ $frameHeight }}px;transform:scale({{ $scale }});transform-origin:top left;border:0;pointer-events:none;background:#fff;"
    ></iframe>
</div>
