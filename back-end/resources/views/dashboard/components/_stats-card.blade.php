<div class="bg-white p-4 rounded shadow text-center">
  <div class="text-sm text-gray-500">{{ $label }}</div>
  <div class="text-2xl font-bold">{{ $value }}</div>
  @if($link)
    <a href="{{ $link }}" class="text-xs text-blue-600 mt-2 inline-block">عرض</a>
  @endif
</div>
