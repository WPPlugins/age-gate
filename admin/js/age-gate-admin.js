!function(t){"use strict";jQuery(document).ready(function(t){jQuery(".colour-picker").wpColorPicker(),jQuery(".upload_image_button").on("click",function(e){var a,i,n=t(e.target).data("option"),o=wp.media.model.settings.post.id,r=t('.image_attachment_id[data-option="'+n+'"]').val();if(e.preventDefault(),a)return a.uploader.uploader.param("post_id",r),void a.open();wp.media.model.settings.post.id=r,(a=wp.media.frames.file_frame=wp.media({title:"Select a image to upload",button:{text:"Use this image"},multiple:!1})).on("select",function(){i=a.state().get("selection").first().toJSON(),t('.image-preview[data-option="'+n+'"]').attr("src",i.url).css("width","auto"),t('.image_attachment_id[data-option="'+n+'"]').val(i.id),wp.media.model.settings.post.id=o}),a.open()}),jQuery("a.add_media").on("click",function(){wp.media.model.settings.post.id=wp_media_post_id}),jQuery(".remove-image").on("click",function(){var e=t(event.target).data("option");t('.image-preview[data-option="'+e+'"]').attr("src",""),t('.image_attachment_id[data-option="'+e+'"]').val("")}),t('[data-action="link-modal"]').on("click",function(e){e.preventDefault(),wpActiveEditor=!0,wpLink.open("wpwrap"),wpLink.textarea=t("body"),t("#wp-link-text").prop("readonly",1)}),t("#wp-link-submit").on("click",function(e){e.preventDefault();var a,i,n,o="";return a=t(".query-results li.selected").find(".item-title").text(),i=wpLink.getAttrs(),t("#ag_linkhref").val(i.href),t("#ag_linktitle").val(a),n={title:a,url:i.href,target:!!i.target},o+="<p><strong>"+(a||"Custom")+"</strong> ("+n.url+")</p>",t(".link-container").html(o),t('[data-action="remove-link"]').length||t('[data-action="link-modal"]').after('<button type="button" class="button remove" data-action="remove-link">Remove link</button>'),!0}),t("body").on("click",'[data-action="remove-link"]',function(e){e.preventDefault(),t(".link-container").empty(),t("#ag_linkhref").val(""),t("#ag_linktitle").val(""),t(e.target).remove()})})}(jQuery);