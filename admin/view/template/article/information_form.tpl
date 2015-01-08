<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-article" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-article" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-data" data-toggle="tab"><?php echo $tab_data; ?></a></li>
            <li><a href="#tab-image" data-toggle="tab"><?php echo $tab_image; ?></a></li>
            <li><a href="#tab-related" data-toggle="tab"><?php echo $tab_related; ?></a></li>
            <li><a href="#tab-youtube" data-toggle="tab"><?php echo $tab_youtube; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active in" id="tab-general">
              <ul class="nav nav-tabs" id="language">
                <?php foreach ($languages as $language) { ?>
                <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
                <?php } ?>
              </ul>
              <div class="tab-content">
                <?php foreach ($languages as $language) { ?>
                <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-name<?php echo $language['language_id']; ?>"><?php echo $entry_name; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="article_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($article_description[$language['language_id']]) ? $article_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_name[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-description<?php echo $language['language_id']; ?>"><?php echo $entry_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="article_description[<?php echo $language['language_id']; ?>][description]" placeholder="<?php echo $entry_description; ?>" id="input-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($article_description[$language['language_id']]) ? $article_description[$language['language_id']]['description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo $entry_meta_title; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="article_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($article_description[$language['language_id']]) ? $article_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
                      <?php if (isset($error_meta_title[$language['language_id']])) { ?>
                      <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-description<?php echo $language['language_id']; ?>"><?php echo $entry_meta_description; ?></label>
                    <div class="col-sm-10">
                      <textarea name="article_description[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($article_description[$language['language_id']]) ? $article_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-meta-keyword<?php echo $language['language_id']; ?>"><?php echo $entry_meta_keyword; ?></label>
                    <div class="col-sm-10">
                      <textarea name="article_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($article_description[$language['language_id']]) ? $article_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
            </div>
            <div class="tab-pane fade" id="tab-data">
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_categories; ?></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 150px; overflow: auto;">                    
                    <?php foreach ($categories as $category) { ?>
                    <div class="checkbox">
                      <label>
                        <?php if (in_array($category['category_id'], $article_categories)) { ?>
                        <input type="checkbox" name="article_categories[]" value="<?php echo $category['category_id']; ?>" checked="checked" />
                        <?php echo $category['name']; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="article_categories[]" value="<?php echo $category['category_id']; ?>" />
                        <?php echo $category['name']; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_store; ?></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 150px; overflow: auto;">
                    <div class="checkbox">
                      <label>
                        <?php if (in_array(0, $article_store)) { ?>
                        <input type="checkbox" name="article_store[]" value="0" checked="checked" />
                        <?php echo $text_default; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="article_store[]" value="0" />
                        <?php echo $text_default; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php foreach ($stores as $store) { ?>
                    <div class="checkbox">
                      <label>
                        <?php if (in_array($store['store_id'], $article_store)) { ?>
                        <input type="checkbox" name="article_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" />
                        <?php echo $store['name']; ?>
                        <?php } else { ?>
                        <input type="checkbox" name="article_store[]" value="<?php echo $store['store_id']; ?>" />
                        <?php echo $store['name']; ?>
                        <?php } ?>
                      </label>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_image; ?></label>
                <div class="col-sm-10"><a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                  <input type="hidden" name="image" value="<?php echo $image; ?>" id="input-image" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-keyword"><span data-toggle="tooltip" title="<?php echo $help_keyword; ?>"><?php echo $entry_keyword; ?></span></label>
                <div class="col-sm-10">
                  <input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="<?php echo $entry_keyword; ?>" id="input-keyword" class="form-control" />
                  <?php if ($error_keyword) { ?>
                  <div class="text-danger"><?php echo $error_keyword; ?></div>
                  <?php } ?>                
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-top"><span data-toggle="tooltip" title="<?php echo $help_top; ?>"><?php echo $entry_top; ?></span></label>
                <div class="col-sm-10">
                  <div class="checkbox">
                    <label>
                      <?php if ($top) { ?>
                      <input type="checkbox" name="top" value="1" checked="checked" id="input-top" />
                      <?php } else { ?>
                      <input type="checkbox" name="top" value="1" id="input-top" />
                      <?php } ?>
                      &nbsp; </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="status" id="input-status" class="form-control">
                    <?php if ($status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-image">
              <div class="table-responsive">
                <table id="images" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_image; ?></td>
                      <td class="text-right"><?php echo $entry_sort_order; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $image_row = 0; ?>
                    <?php foreach ($article_images as $article_image) { ?>
                    <tr id="image-row<?php echo $image_row; ?>">
                      <td class="text-left"><a href="" id="thumb-image<?php echo $image_row; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $article_image['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="article_image[<?php echo $image_row; ?>][image]" value="<?php echo $article_image['image']; ?>" id="input-image<?php echo $image_row; ?>" /></td>
                      <td class="text-right"><input type="text" name="article_image[<?php echo $image_row; ?>][sort_order]" value="<?php echo $article_image['sort_order']; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
                      <td class="text-left"><button type="button" onclick="$('#image-row<?php echo $image_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $image_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="2"></td>
                      <td class="text-left"><button type="button" onclick="addImage();" data-toggle="tooltip" title="<?php echo $button_image_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div class="tab-pane" id="tab-related">
              <div class="form-group">
                <label class="col-lg-2 control-label" for="input-related"><span data-toggle="tooltip" title="<?php echo $help_related; ?>"><?php echo $entry_related; ?></span></label>
                <div class="col-lg-10">
                  <input type="text" name="related" value="" placeholder="<?php echo $entry_related; ?>" id="input-related" class="form-control" />                  
                </div>
              </div>
              <div class="table-responsive">
                <table id="related" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_name; ?></td>
                      <td class="text-right"><?php echo $entry_sort_order; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $related_row = 0; ?>
                    <?php foreach ($article_relates as $related) { ?>
                      <tr id="image-row<?php echo $related_row; ?>">
                        <td class="text-left"><?php echo $related['title']; ?><input type="hidden" name="article_related[<?php echo $related_row; ?>][article_id]" value="<?php echo $related['article_id']; ?>" /></td>                        
                        <td class="text-right"><input type="text" name="article_related[<?php echo $related_row; ?>][sort_order]" value="<?php echo $related['sort_order']; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
                        <td class="text-left"><button type="button" onclick="$('#image-row<?php echo $related_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                      </tr>
                    <?php $related_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="3"></td>
                    </tr>
                  </tfoot>                  
                </table>
              </div>
            </div>
            <div class="tab-pane" id="tab-youtube">
              <div class="table-responsive">
                <table id="youtubes" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_image; ?></td>
                      <td class="text-left"><span data-toggle="tooltip" title="<?php echo $help_youtube; ?>"><?php echo $entry_youtube_url; ?></span></td>
                      <td class="text-left"><?php echo $entry_youtube_controls; ?></td>
                      <td class="text-left"><?php echo $entry_youtube_autoplay; ?></td>
                      <td class="text-right"><?php echo $entry_sort_order; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $youtube_row = 0; ?>
                    <?php foreach ($article_youtubes as $article_youtube) { ?>
                    <tr id="image-row<?php echo $youtube_row; ?>">
                      <td class="text-left"><a href="" id="youtube-thumb-image<?php echo $youtube_row; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $article_youtube['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="article_youtube[<?php echo $youtube_row; ?>][image]" value="<?php echo $article_youtube['image']; ?>" id="youtube-input-image<?php echo $youtube_row; ?>" /></td>
                      <td class="text-right"><input type="text" name="article_youtube[<?php echo $youtube_row; ?>][url]" value="<?php echo $article_youtube['url']; ?>" placeholder="<?php echo $help_youtube; ?>" class="form-control" /></td>
                      <td class="text-right"><input type="checkbox" name="article_youtube[<?php echo $youtube_row; ?>][controls]" value="1" placeholder="<?php echo $entry_youtube_controls; ?>" class="form-control" <?php echo ($article_youtube['controls']==1)? 'checked="checked"' : ''; ?> /></td>
                      <td class="text-right"><input type="checkbox" name="article_youtube[<?php echo $youtube_row; ?>][autoplay]" value="1" placeholder="<?php echo $entry_youtube_autoplay; ?>" class="form-control" <?php echo ($article_youtube['autoplay']==1)? 'checked="checked"' : ''; ?> /></td>
                      <td class="text-right"><input type="text" name="article_youtube[<?php echo $youtube_row; ?>][sort_order]" value="<?php echo $article_youtube['sort_order']; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
                      <td class="text-left"><button type="button" onclick="$('#image-row<?php echo $youtube_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                    <?php $youtube_row++; ?>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="5"></td>
                      <td class="text-left"><button type="button" onclick="addYoutube();" data-toggle="tooltip" title="<?php echo $button_youtube_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
var related_row = <?php echo $related_row; ?>;

$('input[name=\'related\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=article/information/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        json.unshift({
          article_id: 0,
          name: '<?php echo $text_none; ?>'
        });

        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['article_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    console.log(item);
    html  = '<tr id="related_row' + related_row + '">';
    html += '  <td class="text-left">' + item['label'] + '<input type="hidden" name="article_related[' + related_row + '][article_id]" value="' + item['value'] + '" /></td>';
    html += '  <td class="text-right"><input type="text" name="article_related[' + related_row + '][sort_order]" value="0" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
    html += '  <td class="text-left"><button type="button" onclick="$(\'#related_row' + related_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';

    $('#related tbody').append(html);

    related_row++;
  }
});
//--></script> 
<script type="text/javascript"><!--
var youtube_row = <?php echo $youtube_row; ?>;

function addYoutube() {
  html  = '<tr id="youtube_row' + youtube_row + '">';
  html += '  <td class="text-left"><a href="" id="youtube-thumb-image' + youtube_row + '"data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /><input type="hidden" name="article_youtube[' + youtube_row + '][image]" value="" id="youtube-input-image' + youtube_row + '" /></td>';
  html += '  <td class="text-right"><input type="text" name="article_youtube[' + youtube_row + '][url]" value="" placeholder="<?php echo $help_youtube; ?>" class="form-control" /></td>';
  html += '  <td class="text-right"><input type="checkbox" name="article_youtube[' + youtube_row + '][controls]" value="1" placeholder="<?php echo $entry_youtube_controls; ?>" class="form-control" /></td>';
  html += '  <td class=""><input type="checkbox" name="article_youtube[' + youtube_row + '][autoplay]" value="" placeholder="<?php echo $entry_youtube_autoplay; ?>" class="form-control" /></td>';
  html += '  <td class="text-right"><input type="text" name="article_youtube[' + youtube_row + '][sort_order]" value="1" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
  html += '  <td class="text-left"><button type="button" onclick="$(\'#youtube_row' + youtube_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';

  $('#youtubes tbody').append(html);
  
  youtube_row++;
}
//--></script>
<script type="text/javascript"><!--
var image_row = <?php echo $image_row; ?>;

function addImage() {
  html  = '<tr id="image-row' + image_row + '">';
  html += '  <td class="text-left"><a href="" id="thumb-image' + image_row + '"data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /><input type="hidden" name="article_image[' + image_row + '][image]" value="" id="input-image' + image_row + '" /></td>';
  html += '  <td class="text-right"><input type="text" name="article_image[' + image_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
  html += '  <td class="text-left"><button type="button" onclick="$(\'#image-row' + image_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';
  
  $('#images tbody').append(html);
  
  image_row++;
}
//--></script>   
<script type="text/javascript"><!--
<?php foreach ($languages as $language) { ?>
$('#input-description<?php echo $language['language_id']; ?>').summernote({
	height: 300
});
<?php } ?>
//--></script> 
<script type="text/javascript"><!--
$('#language a:first').tab('show');
//--></script></div>
<?php echo $footer; ?>