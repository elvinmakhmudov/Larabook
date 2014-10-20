<div class="dialog-nav-bar">

    <div class="dropdown">
      <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
        Actions
        <span class="caret"></span>
      </button>
      <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Select</a></li>
        <li role="presentation" class="divider"></li>
        {{ Form::open(['method' => 'DELETE', 'route' => ['inbox_path']]) }}
            {{ Form::hidden('convToDelete', $conv->id) }}
            <button class='button' type="submit" >Delete conversation</button>
        {{ Form::close() }}
      </ul>
    </div>

</div>
