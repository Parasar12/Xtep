@foreach ($categories as $category)
<option value="{{ $category->id }}" @if($category->id == $c->parent_id) selected @endif>{{ $category->name }}</option>
@foreach ($category->children as $childCategory)
<option value="{{ $childCategory->id }}" @if($childCategory->id == $c->parent_id) selected @endif>{{ $category->name }} &#8594; {{ $childCategory->name }}</option>
@if ($childCategory->categories)
@foreach ($childCategory->categories as $cCategory)
<option value="{{ $cCategory->id }}" @if($cCategory->id == $c->parent_id) selected @endif>{{ $category->name }} &#8594; {{ $childCategory->name }} &#8594; {{ $cCategory->name }}</option>
@if($cCategory->categories)
@foreach($cCategory->categories as $scCategory)
<option value="{{ $scCategory->id }}" @if($scCategory->id == $c->parent_id) selected @endif>{{ $category->name }} &#8594; {{ $childCategory->name }} &#8594; {{ $cCategory->name }} &#8594; {{ $scCategory->name }}</option>
@endforeach
@endif
@endforeach
@endif
@endforeach
@endforeach