@foreach($objects as $obj)
	<label class="item" data-parent-id="column-objects" data-guid="{{$obj->id}}">
		{{$obj->business_name}}
	</label>
@endforeach