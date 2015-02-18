<?php

$req = $app->get('request')->getUrl();
$req = str_replace('/form', '', $req);

?><form method="post" action="<?php echo $req; ?>/saveform">
    <div class="form-group">
        <label for="exampleInputEmail1">Email address</label>
        <input type="email" class="form-control" name="exampleInputEmail1" placeholder="Enter email">
    </div>
    <div class="form-group">
        <label for="exampleInputName1">Name</label>
        <input type="text" class="form-control" name="exampleInputName1" placeholder="Your name">
    </div>
    <div class="form-group">
        <label for="exampleInputFile">File input</label>
        <input type="file" name="exampleInputFile">
        <p class="help-block">Example block-level help text here.</p>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="checkbox"> Check me out
        </label>
    </div>
    <button type="submit" class="btn btn-default">Submit</button>
</form>