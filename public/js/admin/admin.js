$(document).ready(function() {
	$(".addcard_wrapper .content_header h1 button").on("click", function() {
		$(".addcard_wrapper .sidebar").toggleClass("mobile");
		$(this).toggleClass("mobile");
		if ($(this).find("img").attr("src") == "http://jobgrouper.com/img/Category/ham.png") {
			$(this).find("img").attr("src", "http://jobgrouper.com/img/View/cancel.png");
		}	else {
			$(this).find("img").attr("src", "http://jobgrouper.com/img/Category/ham.png");
		}
	});

    $(".addcard_wrapper .content_form .list_wrapper__item .item_info .buttons >a >button").click(function() {
        localStorage.setItem("cat_name", $(this).parents(".item_info").find(".title").text());
    });
    console.log(localStorage);

    if (window.location.href.indexOf("http://jobgrouper.com/admin/card/") !== -1) {
        console.log($("#category option:contains('" + localStorage.cat_name + "')"))
        $("#category").remove();
        $("#category option:contains('" + localStorage.cat_name + "')").attr("selected", "selected");
    }


	$(".admintext_wrapper__item .edit").on("click", function() {
        $(this).parent().find(".save").fadeIn("fast");
        $(this).parent().find(".cancel").fadeIn("fast");
        if ($(window).innerWidth() < 992) {
             $(this).parent().css("padding-bottom", "75px");
        }
        $(this).hide();
		$(this).parent().find("p").hide();
		$(this).parent().find("input").val($(this).parent().find("p").text()).fadeIn("fast");
	});

    $(".admintext_wrapper__item .cancel").on("click", function() {
        $(this).parent().find("p").show();
        $(this).parent().find("input").hide();
        if ($(window).innerWidth() < 992) {
            $(this).parent().css("padding-bottom", "30px");
        }
        $(this).hide();
        $(this).prev().hide();
        $(this).parent().find(".edit").fadeIn("fast");
    });

    $("#small-dialog3 .buttons .buttons_cancel").click(function() {
        $.magnificPopup.close();
    });

    $(".admincat_wrapper__new button").click(function(e) {
        e.preventDefault();
        if ($(this).prev().val().trim().length > 0) {
            $(this).parent().submit();
        } else {
            alert("Please enter category name correctly!");
        }
    });

    $(".addcard_wrapper .content_form .add_form .create_buttons button:first-child").click(function(e) {
        e.preventDefault();
        if($("#title").val().trim().length > 0 && $("#descr").val().trim().length > 0 && $("#max").val().trim().length > 0 && $("#per").val().trim().length > 0 && $("#descr").val().trim().length > 0 ) {
            $(this).parent().parent().submit();
        } else {
            alert("Fill all the fields!");
        }
    });

    $(".admintext_wrapper__item .save").on("click", function() {
        $(this).parent().find("p").text($(this).parent().find("input").val()).show();
        $(this).parent().find("input").hide();
        if ($(window).innerWidth() < 992) {
            $(this).parent().css("padding-bottom", "30px");
        }
        $(this).hide();
        $(this).next().hide();
        $(this).parent().find(".edit").fadeIn("fast");
        var obj = {
         value: $(this).parent().find("input").val()
        }
        console.log(obj);
        var currentId = $(this).parent().attr("data-id");
        $.ajax({
             type: "PUT",
             url: "http://jobgrouper.com/api/texts/" + currentId,
             data: obj,
             datatype: "json",
             success: function(response) {
                 console.log(response);
             }
        });

    });

	$(".admincat_wrapper__items .item .buttons button:first-child").on("click", function() {
		$(this).parent().parent().find("p").hide();
		$(this).parent().parent().find(">button").fadeIn("fast");
		$(this).parent().parent().find("input").val($(this).parent().parent().find("p").text()).fadeIn("fast");
	});

	$(".admincat_wrapper__items .item >button").on("click", function() {
		$(this).hide();
		$(this).prev().hide();
		$(this).parent().find("p").text($(this).prev().val());
		$(this).parent().find("p").fadeIn("fast");
	});

    jQuery.fn.ForceNumericOnly =
function()
{
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            return (
                key == 8 || 
                key == 9 ||
                key == 13 ||
                key == 46 ||
                key == 110 ||
                key == 190 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};
    
    $(".addcard_wrapper .content_form .add_form .double .max #max").ForceNumericOnly();
   $(".addcard_wrapper .content_form .add_form .double .perclient #per").ForceNumericOnly();
	setInterval(function(e) {
		if ($(".addcard_wrapper .content_form .add_form .double .max #max").val().trim().length != 0 && $(".addcard_wrapper .content_form .add_form .double .perclient #per").val().trim().length != 0) {
			$(".addcard_wrapper .content_form .add_form .double .salary #salary").val(($(".addcard_wrapper .content_form .add_form .double .perclient #per").val() * +$(".addcard_wrapper .content_form .add_form .double .max #max").val()));
		} else {
			$(".addcard_wrapper .content_form .add_form .double .salary #salary").val("");
		}
	}, 100);
	// $(".addcard_wrapper .content_form .add_form .double .max #max").keyup(function() {
	// 	$(".addcard_wrapper .content_form .add_form .double .salary #salary").val("");
	// 	console.log($(this).val());
	// 	if ($(".addcard_wrapper .content_form .add_form .double .perclient #per").val().trim().length != 0) {
	// 		$(".addcard_wrapper .content_form .add_form .double .salary #salary").val((+$(this).val()*+$(".addcard_wrapper .content_form .add_form .double .perclient #per").val())*0.85);
	// 	}
	// });
	// $(".addcard_wrapper .content_form .add_form .double .perclient #per").keyup(function() {
	// 	console.log($(this).val());
	// 	if ($(".addcard_wrapper .content_form .add_form .double .max #max").val().trim().length != 0) {
	// 		$(".addcard_wrapper .content_form .add_form .double .salary #salary").val((+$(this).val()*+$(".addcard_wrapper .content_form .add_form .double .max #max").val())*0.85);
	// 	}
	// });

	$('.popup-with-move-anim').magnificPopup({
		type: 'inline',

		fixedContentPos: false,
		fixedBgPos: true,

		overflowY: 'auto',

		closeBtnInside: true,
		preloader: false,
		
		midClick: true,
		removalDelay: 300,
		mainClass: 'my-mfp-slide-bottom'
	});


	function readURL(input, callback) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.upload .img_wrapper img').attr('src', e.target.result);
             callback();
        }
        
        reader.readAsDataURL(input.files[0]);
       
    }
  }
	$("#changeavatar").change(function(event){

		// var tmppath = URL.createObjectURL(event.target.files[0]);
  //   $('.upload .img_wrapper img').fadeIn("fast").attr('src',URL.createObjectURL(event.target.files[0]));

    readURL(this, function(){
	    var obj = {
	    	image_hash: $('.upload .img_wrapper img').attr('src')
	    }
	    $("#adminimg").attr("value", obj.image_hash);
		});


		// readURL(this, function(){
	 //    var obj = {
	 //    	image_hash: $('.upload .img_wrapper img').attr('src')
	 //    }
	 //    console.log(obj);
	 //    $.ajax({
		// 		type: "PUT",
		// 		url: "http://jobgrouper.com//api/user/update",
		// 		data: obj,
		// 		datatype: "json",
		// 		success: function(response) {
		// 			console.log(response);
		// 		}
		// 	});
		// });
	});


if (window.innerWidth > 768) {
    $(function () {
    'use strict';

    var isValid = {};

    function checkValid(){
        for(var key in isValid){
            if(!isValid[key]){
                return false;
            }
        }
        return true;
    }
    $(".save_event").on("click", function(e){
        var error = "";
        $(".span-errors").html("");

        $(".form-control").each(function() {
            if (!$(this).val()) {
                var id = $(this).prop("id");
                var id_el = "#"+id;
                isValid[id] = false;
                error = "Please, enter " + $(this).prev().html().slice(0, $(this).prev().html().indexOf("<"));

                scrollDown(id_el);
                var el = $(id_el).nextAll("span");
                $(el).html(error);

                return false;
            }
            else{
                isValid[$(this).prop("id")] = true;
            }
        });
        if(!checkValid()){
        /*
                console.log("here");
                $("#enter_error").html(error);
                $.magnificPopup.open({
                    items: {
                        src: '#message_alert'
                    },
                    type: 'inline',
                    preloader: false,
                    modal: true
                });
                */
                e.preventDefault();
              }
              else{
                $("#event_form").submit();
              }
            });
    $(document).on('click', '.modal-dismiss', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
    });


    var console = window.console || {
        log: function () {
        }
    };
    var $image = $('#image');
    var $download = $('#download');
    var $dataX = $('#dataX');
    var $dataY = $('#dataY');
    var $dataHeight = $('#dataHeight');
    var $dataWidth = $('#dataWidth');
    var $dataRotate = $('#dataRotate');
    var $dataScaleX = $('#dataScaleX');
    var $dataScaleY = $('#dataScaleY');
    var options = {
        aspectRatio: 2.5/1,
        preview: '.img-preview',
        zoomable: false,
        strict: true,
        crop: function (e) {
            $dataX.val(Math.round(e.x));
            $dataY.val(Math.round(e.y));
            $dataHeight.val(Math.round(e.height));
            $dataWidth.val(Math.round(e.width));
            $dataRotate.val(e.rotate);
            $dataScaleX.val(e.scaleX);
            $dataScaleY.val(e.scaleY);
        }
    };


        // Tooltip
        $('[data-toggle="tooltip"]').tooltip();


        // Cropper
        $image.on({
            'build.cropper': function (e) {
                console.log(e.type);
            },
            'built.cropper': function (e) {
                console.log(e.type);
            },
            'cropstart.cropper': function (e) {
                console.log(e.type, e.action);
            },
            'cropmove.cropper': function (e) {
                console.log(e.type, e.action);
            },
            'cropend.cropper': function (e) {
                console.log(e.type, e.action);
            },
            'crop.cropper': function (e) {
                console.log(e.type, e.x, e.y, e.width, e.height, e.rotate, e.scaleX, e.scaleY);
            },
            'zoom.cropper': function (e) {
                console.log(e.type, e.ratio);
            }
        }).cropper(options);


        // Buttons
        if (!$.isFunction(document.createElement('canvas').getContext)) {
            $('button[data-method="getCroppedCanvas"]').prop('disabled', true);
        }

        if (typeof document.createElement('cropper').style.transition === 'undefined') {
            $('button[data-method="rotate"]').prop('disabled', true);
            $('button[data-method="scale"]').prop('disabled', true);
        }


        // Download
        // if (typeof $download[0].download === 'undefined') {
        //   $download.addClass('disabled');
        // }


        // Options
        // $('.docs-toggles').on('change', 'input', function () {
        //   var $this = $(this);
        //   var name = $this.attr('name');
        //   var type = $this.prop('type');
        //   var cropBoxData;
        //   var canvasData;

        //   if (!$image.data('cropper')) {
        //     return;
        //   }

        //   if (type === 'checkbox') {
        //     options[name] = $this.prop('checked');
        //     cropBoxData = $image.cropper('getCropBoxData');
        //     canvasData = $image.cropper('getCanvasData');

        //     options.built = function () {
        //       $image.cropper('setCropBoxData', cropBoxData);
        //       $image.cropper('setCanvasData', canvasData);
        //     };
        //   } else if (type === 'radio') {
        //     options[name] = $this.val();
        //   }

        //   $image.cropper('destroy').cropper(options);
        // });


        // Methods
        $('.docs-buttons').on('click', '[data-method]', function () {
            var $this = $(this);
            var data = $this.data();
            var $target;
            var result;

            if ($this.prop('disabled') || $this.hasClass('disabled')) {
                return;
            }

            if ($image.data('cropper') && data.method) {
                data = $.extend({}, data); // Clone a new one

                if (typeof data.target !== 'undefined') {
                    $target = $(data.target);

                    if (typeof data.option === 'undefined') {
                        try {
                            data.option = JSON.parse($target.val());
                        } catch (e) {
                            console.log(e.message);
                        }
                    }
                }

                result = $image.cropper(data.method, data.option, data.secondOption);

                switch (data.method) {
                    case 'scaleX':
                    case 'scaleY':
                    $(this).data('option', -data.option);
                    break;

                    case 'getCroppedCanvas':
                    if (result) {

                        // console.log(result.toDataURL());
                        $('#imgCode').val(result.toDataURL());

                        $(".addcard_wrapper .content_form .add_form .upload .img_wrapper img").attr("src", result.toDataURL());
                        $("#adminimg").attr("value", result.toDataURL());
                        $(".addcard_wrapper .content_form .add_form .upload .img_wrapper").height(100);
                                        // readURL(this, function(){
                                          //   var obj = {
                                          //    image_hash: result.toDataURL()
                                          //   }
                                          //   console.log(obj);
                                          //   $.ajax({
                                                //  type: "PUT",
                                                //  url: "http://jobgrouper.com//api/user/update",
                                                //  data: obj,
                                                //  datatype: "json",
                                                //  success: function(response) {
                                                //      console.log(response);
                                                //  }
                                                // });
                                            // });

                            // Bootstrap's Modal
                            $(".here").show();
                            $(".here").html(result);
                            $(".here").after("<div class='delete'><i class='fa fa-trash-o' aria-hidden='true'></i>Remove</div>");
                            $(".upload-boxm").hide();
                            $("#getCroppedCanvasModal").modal('hide');
                            $(".delete").click(function () {
                                $(".here").hide();
                                $(this).remove();
                                $(".hide_btn").show();
                                $("#previewwidth").html("");
                                $("#previewheight").html("");
                            });

                            if (!$download.hasClass('disabled')) {
                                $download.attr('href', result.toDataURL('image/jpeg'));
                            }
                          }

                          break;
                        }

                        if ($.isPlainObject(result) && $target) {
                            try {
                                $target.val(JSON.stringify(result));
                            } catch (e) {
                                console.log(e.message);
                            }
                        }

                      }
                    });


        // Keyboard
        $(document.body).on('keydown', function (e) {

            if (!$image.data('cropper') || this.scrollTop > 300) {
                return;
            }

            switch (e.which) {
                case 37:
                e.preventDefault();
                $image.cropper('move', -1, 0);
                break;

                case 38:
                e.preventDefault();
                $image.cropper('move', 0, -1);
                break;

                case 39:
                e.preventDefault();
                $image.cropper('move', 1, 0);
                break;

                case 40:
                e.preventDefault();
                $image.cropper('move', 0, 1);
                break;
            }

        });


        // Import image
        var $inputImage = $('#file');
        var URL = window.URL || window.webkitURL;
        var blobURL;

        if (URL) {
            $inputImage.change(function () {
                $(".hide_btn").hide();
                $("#getCroppedCanvasModal").modal('show');
                var files = this.files;
                var file;

                if (!$image.data('cropper')) {
                    return;
                }

                if (files && files.length) {
                    file = files[0];

                    if (/^image\/\w+$/.test(file.type)) {
                        blobURL = URL.createObjectURL(file);
                        $image.one('built.cropper', function () {

                            // Revoke when load complete
                            URL.revokeObjectURL(blobURL);
                          }).cropper('reset').cropper('replace', blobURL);
                        $inputImage.val('');
                    } else {
                        window.alert('Please choose an image file.');
                    }
                }
            });
        } else {
            $inputImage.prop('disabled', true).parent().addClass('disabled');
        }

      });




    //////
    $(function () {
        var $image = $('#image');
        var cropBoxData;
        var canvasData;

        $('#getCroppedCanvasModal').on('shown.bs.modal', function (event) {
            console.log($("#previewwidth").text());
            console.log($("#previewheight").text());
            $("#target").keypress();
            $("body").trigger(e);
            $image.cropper({
                // aspectRatio: 1,
                // minContainerWidth: 568,
                // minContainerHeight: 300,
                // built: function () {
                //  $image.cropper('setCanvasData', canvasData);
                //  $image.cropper('setCropBoxData', cropBoxData);
                // }


            });
            if(+$("#previewwidth").text() > 800) {
                $(".modal-body").height($(".cropper-bg").height()*(800/+$("#previewwidth").text()));
            } 
            if(+$("#previewwidth").text() <= 800) {
                $(".modal-body").height($(".cropper-bg").height());
            } 
            // console.log($(".modal-body").height());
        }).on('hidden.bs.modal', function () {
            cropBoxData = $image.cropper('getCropBoxData');
            canvasData = $image.cropper('getCanvasData');
            // $(".here").hide();
      //    $(this).remove();
      //    $(".hide_btn").show();
        $("#previewwidth").html("");
        $("#previewheight").html("");
            // $image.cropper('destroy');
            if(+$("#previewwidth").text() > 800) {
                $(".here").css({
                    transform: "scale("+ 800/+$("#previewwidth").text()+")",
                });
                $("#add-event").height($(".here").height()*(800/+$("#previewwidth").text()));
            }
            
            
            $("#add-event").css({
                marginBottom: "30px",
                position: "relative"
            })
            $(".delete").css({
                position: "absolute",
                left: "0",
                bottom: "-30px",
                cursor: "pointer"
            });

          });

    });

    window.URL = window.URL || window.webkitURL;
    var elBrowse = document.getElementById("file"),
    elPreviewWidth = document.getElementById("previewwidth"),
    elPreviewHeight = document.getElementById("previewheight"),
        useBlob = false && window.URL; // `true` to use Blob instead of Data-URL

        function readImage(file) {
            var reader = new FileReader();
            reader.addEventListener("load", function () {
                var image = new Image();
                image.addEventListener("load", function () {
                    var imageInfo1 = +image.width;
                    var imageInfo2 = +image.height;
                    elPreviewWidth.insertAdjacentHTML("beforeend", imageInfo1);
                    elPreviewHeight.insertAdjacentHTML("beforeend", imageInfo2);
                });
                image.src = useBlob ? window.URL.createObjectURL(file) : reader.result;
                if (useBlob) {
                window.URL.revokeObjectURL(file); // Free memory
              }
            });
            reader.readAsDataURL(file);
        }


        elBrowse.addEventListener("change", function () {
            var files = this.files;
            var errors = "";
            if (!files) {
                errors += "File upload not supported by your browser.";
            }
            if (files && files[0]) {
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    if ((/\.(png|jpeg|jpg|gif)$/i).test(file.name)) {
                        readImage(file);
                    } else {
                        errors += file.name + " Unsupported Image extension\n";
                    }
                }
            }
            if (errors) {
                alert(errors);
            }
        });
}else {


  $('#file').change(function(){
    readURL(this, function() {
        var obj = {
            image_hash: $(".addcard_wrapper .content_form .add_form .upload .img_wrapper img").attr("src")
        }
        $(".addcard_wrapper .content_form .add_form .upload .img_wrapper img").attr("src", obj.image_hash);
        $('#imgCode').val("src", obj.image_hash);
        $("#adminimg").attr("value", obj.image_hash); 
        console.log(obj);
    });
  });
}
/////JOB GROUPER PHASE 2

    $(".addcard_wrapper .content_form .add_form .radio_list .radio[id='1']").on("change", function() {
        $(".china_card").fadeOut("fast");
        $(".english_card").fadeIn("fast");
    });

    $(".addcard_wrapper .content_form .add_form .radio_list .radio[id='2']").on("change", function() {
        $(".china_card").fadeIn("fast");
        $(".english_card").fadeIn("fast");
    });

    $(".addcard_wrapper .content_form .add_form .radio_list .radio[id='3']").on("change", function() {
        $(".china_card").fadeIn("fast");
        $(".english_card").fadeOut("fast");
    });

});