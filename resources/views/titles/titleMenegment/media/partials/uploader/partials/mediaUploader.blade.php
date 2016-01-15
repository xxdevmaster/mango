<div class="form-group" id="titlesBlock">
    <div id="titlesWithSearch">
        <input id="serachbox" type="text" class="form-control" placeholder="Search">
        <select id="titles" name="titles" size="5" class="form-control">
            @if(isset($media['uploader']))
                @foreach($media['uploader'] as $value)
                    @if(!empty($value->locale) && array_key_exists($value->locale, $allLocales))
                        <option value="{{ isset($film->id) ? $film->id : ''}}" data-movieid="{{ $value->id  }}" data-locale="{{ $value['locale']  }}" data-track="{{ isset($value['track']) ? $value['track'] : 0  }}">
                            {{ isset($film->title) ? $film->title : '' }} - {{ $allLocales[$value->locale] }} {{ !empty($value->track_index) ? ' - '. $value->track_index : '' }}
                        </option>
                    @endif
                @endforeach
            @endif
        </select>
    </div>
</div>