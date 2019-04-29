@foreach($objects as $selected)
	<label class="item" data-guid="{{$type == 'Plan' ? $selected->id : $selected->guid}}">
		@if($type == "Plan") 
			{{$selected->business_name}}
		@else
			{{$selected->name}}
		@endif
	</label>
@endforeach