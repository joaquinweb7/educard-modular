<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Carnets Estudiantiles</title>
    <style>
        @if(isset($fonts))
            @foreach($fonts as $font)
            @font-face {
                font-family: '{{ $font->name }}';
                src: url('{{ str_replace('\\', '/', public_path('storage/' . $font->file_path)) }}');
                font-weight: normal;
                font-style: normal;
            }
            @endforeach
        @endif
        * { box-sizing: border-box; margin: 0; padding: 0; }
        @page {
            size: {{ $template->width * 0.75 }}pt {{ $template->height * 0.75 }}pt;
            margin: 0px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #ffffff;
            margin: 0px;
            padding: 0px;
            -webkit-print-color-adjust: exact;
        }
        .card-page {
            width: {{ $template->width }}px;
            height: {{ $template->height }}px;
            position: relative;
            page-break-after: always;
            overflow: hidden;
        }
        .card-page:last-child {
            page-break-after: auto;
        }
        .carnet-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .cv-element {
            position: absolute;
            line-height: 1.2;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
@foreach($students as $student)
    @php
        $vals = $renderer->values($student);
    @endphp
    <div class="card-page">
        {{-- Background --}}
        @if($template->background_path)
            <img class="carnet-bg" src="{{ public_path('storage/'.$template->background_path) }}" alt="Fondo">
        @else
            <div class="carnet-bg" style="background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);"></div>
        @endif

        {{-- Dynamic Elements --}}
        @foreach($renderer->objects($template) as $el)
            @php
                $w = (isset($el['width']) && $el['width'] > 0) ? $el['width'] . 'px' : 'auto';
                $opacity = isset($el['opacity']) ? $el['opacity'] / 100 : 1;
                $left = $el['x'] ?? 0;
                $top = $el['y'] ?? 0;
                $fontSize = $el['fontSize'] ?? 14;
                $fontWeight = $el['fontWeight'] ?? '400';
                $color = $el['color'] ?? '#ffffff';
                $textAlign = $el['textAlign'] ?? 'left';
                $fontFamily = $el['fontFamily'] ?? 'sans-serif';
                $background = $el['background'] ?? 'transparent';
                $borderRadius = $el['borderRadius'] ?? 0;
                $padding = $el['padding'] ?? 0;
                $whiteSpace = (isset($el['width']) && $el['width'] > 0) ? 'normal' : 'nowrap';
            @endphp
            
            <div class="cv-element" style="left: {{ $left }}px; top: {{ $top }}px; font-size: {{ $fontSize }}px; font-weight: {{ $fontWeight }}; color: {{ $color }}; text-align: {{ $textAlign }}; font-family: {{ $fontFamily }}; opacity: {{ $opacity }}; width: {{ $w }}; background: {{ $background }}; border-radius: {{ $borderRadius }}px; padding: {{ $padding }}px; white-space: {{ $whiteSpace }};">
                @if(($el['type'] ?? '') === 'photo')
                    @if($vals['photo'])
                        <img src="{{ public_path('storage/'.$vals['photo']) }}" 
                             style="width: 100%; display: block; border-radius: {{ $borderRadius }}px;">
                    @else
                        <div style="width: 100%; text-align: center; color: #aaa; padding: 10px 0; font-size: 11px; background: rgba(0,0,0,0.1); border: 1px dashed #ccc;">
                            [SIN FOTO]
                        </div>
                    @endif
                @else
                    {!! nl2br(e($renderer->processContent($el['content'] ?? '', $vals))) !!}
                @endif
            </div>
        @endforeach
    </div>
@endforeach
</body>
</html>
