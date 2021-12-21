<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label for="name" class="required">Meta </label>
            <input type="text" name="name" id="name" autofocus required class="form-control" value="{{ old('name', $mark->name) }}">
        </div>
    </div>
    <div class="form-group col-12">
        <label for="goal_id">Objetivos </label>
        
        <select {{ $goals->isEmpty() ? "disabled" : "" }} class="form-control select2 select2-goal multiple" multiple name="goal_id[]" id="goal_id" value="{{ json_encode(old('goal_id', $mark->goals->pluck('id'))) }}">
            <option class="selected"></option>
            @foreach($goals as $goal)
                <option value="{{ $goal->id }}">{{ $goal->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-sm-6 col-12">
        <label for="dimension" class="required">Dimensão </label>
        <select class="form-control select2 select2-dimension" name="dimension" id="dimension" required value="{{ old('dimension', $mark->dimension) }}">
            <option class="selected"></option>
            @foreach(App\Models\Mark::defaultDimensions() as $value => $dimension)
                <option value="{{ $value }}">{{ $dimension }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-sm-6 col-12">
        <label for="user_id" class="required">Responsável </label>
        <select class="form-control select2 select2-user" name="user_id" id="user_id" required value="{{ old('user_id', $mark->user_id) }}">
            <option class="selected"></option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-sm-6 col-12">
        <label for="status" class="required">Status </label>
        <select class="form-control select2 select2-status disabled" disabled id="status" value="{{ old('status', $mark->status) }}">
            <option class="selected"></option>
            @foreach(App\Models\Mark::defaultStatuses() as $value => $status)
                <option value="{{ $value }}">{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-sm-6 col-12">
        <label for="finantial_value" class="required">Valor financeiro </label>
        <input type="number" name="finantial_value" id="finantial_value" required class="form-control" value="{{ old('finantial_value', $mark->finantial_value) }}">
    </div>
    <div class="form-group col-sm-6 col-12">
        <label for="predicted_value" class="required">Valor Estimado </label>
        <input type="number" name="predicted_value" id="predicted_value" required class="form-control" value="{{ old('predicted_value', $mark->predicted_value) }}">
    </div>
    <div class="form-group col-sm-6 col-12">
        <label for="achieved_value" class="required">Valor alcançado </label>
        <input type="number" name="achieved_value" id="achieved_value" required class="form-control" value="{{ old('achieved_value', $mark->achieved_value) }}">
    </div>
    <div class="form-group col-sm-6 col-12">
        <label for="deadline" class="required">Data limite </label>
        <input type="date" name="deadline" id="deadline" class="form-control" required value="{{ old('deadline',$mark->deadline) }}">
    </div>
    <div class="form-group col-sm-6 col-12 my-auto">  
        <label for="file_path">Evidências</label>
        <input type="file" class="form-control-file" name="file_path">
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function(){
            $(".select2-dimension").select2({
                placeholder: "Selecione uma Dimensão",
            });

            $(".select2-user").select2({
                placeholder: "Selecione um Responsável",
            });

            $(".select2-status").select2({
                placeholder: "Selecione um Status",
            });

            $(".select2-goal").select2({
                placeholder: "Selecione Objetivos",
            });
        });


        $('select[value]').each(function () {
            $(this).val($(this).attr('value'));
        });

        $('.multiple[value]').each(function(){
                var value = $(this).attr('value');

                if (value) {
                    value = JSON.parse(value);
                }

                $(this).val(value);
            })
        
    </script>
@endpush

