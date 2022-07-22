{{-- relationships with pivot table (n-n) --}}
@php
    $column['value'] = $column['value'] ?? data_get($entry, $column['name'], []);
    $column['escaped'] = $column['escaped'] ?? true;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';
    $column['limit'] = $column['limit'] ?? 32;
    $column['attribute'] = $column['attribute'] ?? (new $column['model'])->identifiableAttribute();

    if($column['value'] instanceof \Closure) {
        $column['value'] = $column['value']($entry);
    }

    if($column['value'] !== null && !$column['value']->isEmpty()) {
        $related_key = $column['value']->first()->getKeyName();
        $column['value'] = $column['value']->pluck($column['attribute'], $related_key);
    }

    $column['value'] = $column['value']
        ->each(function($value) use ($column) {
            $value = Str::limit($value, $column['limit'], '…');
        })
        ->toArray();
    
    $exportString = function() use ($column, $crud, $entry) {
        $string = '<div class="d-inline-flex">';
        $string .= e($column['prefix']);
        foreach($column['value'] as $key => $text){
            if(!empty($column['wrapper'])) {
                $string.= view('crud::columns.inc.wrapper_start', ['crud' => $crud, 'column' => $column, 'entry' => $entry, 'relatedKey' => $key])->render();
            }
            if($column['escaped']) {
                $string .= e($text);
            }else{
                $string .= $text;
            }
            if (!$key === array_key_last($column['value'])) {
                echo ',';
            }
            if(!empty($column['wrapper'])) {
                $string.= view('crud::columns.inc.wrapper_end', ['crud' => $crud, 'column' => $column, 'entry' => $entry, 'relatedKey' => $key])->render();
            }
        }
        $string .= e($column['prefix']);
        $string .= '</div>';
        return $string;
    };

    if(!empty($column['value'])) {
        echo $exportString();
    } else {
        echo $column['default'] ?? '-';
    }
@endphp


    

