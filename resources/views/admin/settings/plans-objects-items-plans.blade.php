@foreach($plans as $plan)
	<label class="item" data-parent-id="column-plans" data-guid="{{$plan->guid}}">
		{{$plan->name}}
	</label>
@endforeach