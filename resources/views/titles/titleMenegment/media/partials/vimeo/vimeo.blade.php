<form id="vimeoForm" role="form">
    <input type="hidden" name="filmId" value="{{ isset($film->id) ? $film->id : '' }}">
    <div class="form-group">
        <label for="trailerVimeo">Trailer Vimeo</label>
        <input type="text" class="form-control" id="trailerVimeo" name="trailerVimeo" placeholder="" value="{{ isset($film->trailerVimeo) ? $film->trailerVimeo : '' }}">
    </div>
    <div class="form-group">
        <label for="movieVimeo">Feature Vimeo</label>
        <input type="text" class="form-control" id="movieVimeo" name="movieVimeo" placeholder="" value="{{ isset($film->movieVimeo) ? $film->movieVimeo : '' }}">
    </div>
</form>