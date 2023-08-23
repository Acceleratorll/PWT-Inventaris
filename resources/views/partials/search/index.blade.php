<form name="search-form" id="search-form" action="{{ $action }}" method="get">
    @csrf
    <label for="term">Search</label>
    <input type="text" name="term" id="term">
    <button type="submit">Submit</button>
</form>