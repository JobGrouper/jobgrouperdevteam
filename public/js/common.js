$(document).ready(function() {

	var d = $('.message_chat .all_sms');
	d.scrollTop(d.prop("scrollHeight"));
	$(".dark_header__line .ham").click(function() {
		$(".dark_header__categories").fadeToggle("fast");
		$(".main_header__logo a img").toggleClass("mobileshow");
		$(".main_header__logo a").toggleClass("mobileshow");
		$(".dark_bg").toggleClass("active");
		if ($(this).find("img").attr("src") == "http://jobgrouper.com/img/Category/ham.png") {
			$(this).find("img").attr("src", "http://jobgrouper.com/img/View/cancel.png");
		}	else {
			$(this).find("img").attr("src", "http://jobgrouper.com/img/Category/ham.png");
		}
		if (window.innerWidth < 768 && window.location.href == "http://jobgrouper.com/") {
			$(".main_header__logo").toggleClass("hide_logo");
			$(".main_header .dark_header__categories").toggleClass("active");
		}
		
	});

	$(".view_sidebar .buttons .need").click(function() {
		window.location.href = '/purchase/' + $(this).attr("purchase");
	});

	// $(".job_item h1").click(function() {
	// 	$(this).parents(".job_item").find(".jobs_acc").slideToggle("fast");
	// 	$(this).parent().toggleClass("active");
	// 	$(this).toggleClass("active");
	// });

	if (window.innerWidth < 768 && window.location.href == "http://jobgrouper.com/") {
		$("body").on("click", ".dark_bg.active", function() {
			$(".main_header .dark_header__categories").removeClass("active");
			$(this).removeClass("active");
			$(".dark_header__categories").fadeOut("fast");
			$(".main_header__logo a img").removeClass("mobileshow");
			$(".main_header__logo a").removeClass("mobileshow");
			$(".main_header__logo").removeClass("hide_logo");
			$(".main_header .dark_header__line .ham").find("img").attr("src", "http://jobgrouper.com/img/Category/ham.png");
			console.log(123);
		});
		$("body").on("click", "dark_header__categories.active", function(e) {
			e.stopPropagation();
		});
	}
	
	
	$(".job_item h1 .request_close.leave_api").click(function() {
		var job_id = $(this).attr("job-id");
		var self = $(this);
		$.ajax({
			type: "POST",
			url: "/api/employeeExitRequest/" + job_id,
			datatype: "json",
			success: function(response) {
				console.log(response);
				if(response.status == 0) {
					self.hide();
					self.next().fadeIn("fast");
				} else {
					self.parent().parent().hide();
				}
			}
		});
	});

	$(".job_item h1 .request_close.close_api").click(function() {
		var job_id = $(this).attr("request-id");
		var self = $(this);
		$.ajax({
			type: "DELETE",
			url: "/api/employeeRequest/" + job_id,
			datatype: "json",
			success: function(response) {
				console.log(response);
				if(response.status == 0) {
					self.parent().parent().hide();
					// self.next().fadeIn("fast");
					// alert(response.info);
				}
			}
		});
	});

	var starLength = 0;
	$("#small-dialog4 .stars .wrapper .star").click(function() {
		$("#small-dialog4 .stars .yellow").width(($(this).index() + 1)*20 + "%");
		starLength = $(this).index() + 1;
	});



	var ratePersonId;
	var orderCloseId;
	var selfId;
	$(".cancelbtn").click(function() {
		$("#small-dialog4 .stars .yellow").width(0);
		var order_id = $(this).attr('data-order_id');
		orderCloseId = $(this).parents(".workers_item").attr("data-id");
		selfId = $(this);
		if($('#block_' + order_id).attr('data-hasEmployee') != 0){
			$("#small-dialog4 .header .name").text($(this).parents(".workers_item").find(".rating_name span").text());
			ratePersonId = $(this).parents(".workers_item").attr("data-hasemployee");
		}
		else{
			$(this).parent().removeClass("popup-with-move-anim").attr("href", "");
			ratePersonId = $(this).parents(".workers_item").attr("data-id");
			if (confirm("Are you sure you want to close this order?")){
				var obj = {
				score: starLength,
				comment: $("#small-dialog4 textarea").val()
				}
				console.log(obj);
				$.ajax({
					type: "POST",
					url: "/api/closeOrderRequest/" + ratePersonId,
					datatype: "json",
					success: function(response) {
						console.log(response);
						$.magnificPopup.close();
						if (response.error == false) {
							selfId.parent().parent().submit();
							// $.ajax({
							// 	type: "POST",
							// 	url: "/api/closeOrderRequest/" + orderCloseId,
							// 	dataType: "json",
							// 	success: function(response) {
							// 		console.log(response);
							// 		if (response.error == false) {
							// 			console.log(123);
							// 			selfId.parents(".workers_item").find(".purchasebtn").hide();
							// 			selfId.hide();
							// 			selfId.next().fadeIn("fast");
							// 			alert("You have declined your participation in this project. Your request has been sent successfully to the administrator!");
							// 		} else {
							// 			alert(response.info);
							// 		}
							// 	}
							// });
						}
					}
				});
			};
		}
	});

	$("#small-dialog4 .send").on("click", function() {
		var obj = {
			score: starLength,
			comment: $("#small-dialog4 textarea").val()
		}
		console.log(obj);
		$.ajax({
			type: "POST",
			url: "/api/rate/" + ratePersonId,
			data: obj,
			datatype: "json",
			success: function(response) {
				console.log(response);
				$.magnificPopup.close();
				if (response.error == false) {
					selfId.parent().parent().submit();
					// $.ajax({
					// 	type: "POST",
					// 	url: "/api/closeOrderRequest/" + orderCloseId,
					// 	dataType: "json",
					// 	success: function(response) {
					// 		console.log(response);
					// 		if (response.error == false) {
					// 			console.log(123);
					// 			selfId.parents(".workers_item").find(".purchasebtn").hide();
					// 			selfId.hide();
					// 			selfId.next().fadeIn("fast");
					// 			alert("You have declined your participation in this project. Your request has been sent successfully to the administrator!");
					// 		} else {
					// 			alert(response.info);
					// 		}
					// 	}
					// });
				}
			}
		});
	});


	/////INDEX POPUP

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
	

	//HELP
	$(".faq .faq_item .bold").click(function() {
		$(this).next().toggleClass("show");
		$(this).parent().find(".text_block").slideToggle();
	});
	$(".faq .faq_item .plus").click(function() {
		$(this).toggleClass("show");
		$(this).parent().find(".text_block").slideToggle();
	});

	$(".view_sidebar .window span, .view_sidebar .salary_info .purchase .block .amount").click(function() {
		$(".alert_window").fadeIn("fast");
	});

	$(".alert_window__block .cancel").click(function() {
		$(".alert_window").fadeOut("fast");
	});
	// With JQuery
	// $("#ex6").slider();
	// $("#ex6").on("slide", function(slideEvt) {
	// 	$("#ex6SliderVal").text(slideEvt.value);
	// });


	////REGISTER START
	$(".signup .terms").on("click", function() {
				$(this).prev().click();

					if ($("#terms").prop("checked") === false) {
					$("#terms").prop("checked", true);
				} 
				else if ($("#terms").prop("checked") === true) {
					$("#terms").prop("checked", false);
				}
				
			});
	$("body").on("click", ".present", function() {
		$(this).prev().click();

		if($(this).prev().prev().val() !== 'present') {
			$(this).prev().prev().val('present')
		} else {
			$(this).prev().prev().val('')
		}

		if ($("#present").prop("checked") === false) {
			$("#present").prop("checked", true);
		}
		else if ($("#present").prop("checked") === true) {
			$("#present").prop("checked", false);
		}

	});
	var userChoose;
	$(".buy_radio").click(function() {
		if ($("#employee").attr("checked", false)) {
			$("#buyer").attr("checked", true);
			userChoose = "buyer";
			console.log(userChoose);
		} else {
			$("#buyer").attr("checked", false);
			console.log($("#buyer").attr("checked"));
		}
	});

	function isEmail(email) {
  	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  	return regex.test(email);
	}

	$(".employee_radio").click(function() {
		if ($("#buyer").attr("checked", false)) {
			$("#employee").attr("checked", true);
			userChoose = "employee";
			console.log(userChoose);
		} else {
			$("#employee").attr("checked", false);
			console.log($("#employee").attr("checked"));
		}
		
	});

	$(".signup #email").focusout(function() {
		var obj = {
			email: $("#email").val(),
		}
		$.ajax({
			type: "POST",
			url: "/api/checkEmailFree",
			data: obj,
			datatype: "json",
			success: function(response) {
				console.log(response);
				if(response.status == 1) {
					$(".signup .invalid_login").text(response.info);
				} else {
					$(".signup .invalid_login").html("");
				}
		}
		});
	});

	function testAjax(event) {
		var obj = {
				email: $("#email").val(),
			}
		$.ajax({
				type: "POST",
				url: "/api/checkEmailFree",
				data: obj,
				datatype: "json",
				success: function(response) {
					console.log(response , "lalal");
					if(response.status == 1) {
						console.log(123);
						// $(".signup .invalid_login").text(response.info);
						$(".signup .invalid_login").html(response.info);
						return false;

					} else {
						// console.log(response.info);
						$("form").submit();
						
					}
			}
			});
	}
	$(".signup .btndiv button").click(function(event) {
		// event.preventDefault();
		var checkedAttr = $("input[class='radio']").attr("checked");
		// console.log(checkedAttr);
		if ($("#first").val().trim().length > 0 && $("#last").val().trim().length > 0 && isEmail($("#email").val().trim()) == true && $("#pass").val().trim().length > 0 && $("#conpass").val().trim().length > 0 && $("#conpass").val().trim() == $("#pass").val().trim() && userChoose != undefined && $(".terms").prev().prop("checked") === true && $(".invalid_login").html() == "") {
			// $.ajax({
			// 	type: "POST",
			// 	url: "http://api.jobgrouper.com/register",
			// 	data: obj,
			// 	datatype: "json",
			// 	success: function(response) {
			// 		console.log(response);
			// 	}
			// })
			event.preventDefault();
			testAjax(event);
		} else {
			alert("Fill all the fields correctly!");
			event.preventDefault();
		}
	});

	////REGISTER FINISH


	/// PROFILE EDIT

	$(".profile_info .edit_profile").click(function() {
		$(".profile_info .social .name").hide();
		$(".profile_info .social .edittitle").fadeIn("fast");
		$(".profile_info .linkedin span, .profile_info .linkedin >img").hide();
		$(".profile_info .linkedin .editlinkedin").fadeIn("fast");
		$(".profile_info .facebook span, .profile_info .facebook >img").hide();
		$(".profile_info .facebook .editfacebook").fadeIn("fast");
		$(".profile_info .github span, .profile_info .github >img").hide();
		$(".profile_info .github .editgithub").fadeIn("fast");
		$(".profile_info .savebtn_top").fadeIn("fast");
	});


	$(".profile_info .savebtn_top .close").click(function() {
		$(this).parent().hide();
		$(".profile_info .social .name").show();
		$(".profile_info .social .edittitle").hide();
		$(".profile_info .linkedin .editlinkedin").hide();
		$(".profile_info .linkedin span, .profile_info .linkedin >img").show();
		$(".profile_info .facebook .editfacebook").hide();
		$(".profile_info .facebook span, .profile_info .facebook >img").show();
		$(".profile_info .github .editgithub").hide();
		$(".profile_info .github span, .profile_info .github >img").show();
	});


	function readURL(input, callback) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('.profile_info .img_wrapper img').attr('src', e.target.result);
             callback();
        }
        
        reader.readAsDataURL(input.files[0]);

       
    }
  }
    
  



  $(".profile_info .savebtn_top #profiletop_save").click(function() {
  	var obj = {
  		first_name: $(".profile_info .social .edittitle .edit_name").val(),
			last_name: $(".profile_info .social .edittitle .edit_surname").val(),
  		linkid_url: $(".editlinkedin input").val(),
  		fb_url: $(".editfacebook input").val(),
    	git_url: $(".editgithub input").val()
    }
    console.log(obj);
    if($(".profile_info .social .edittitle .edit_name").val().length > 0 || $(".profile_info .social .edittitle .edit_surname").val().length > 0) {
    	 $.ajax({
				type: "PUT",
				url: "/api/user/update",
				data: obj,
				datatype: "json",
				success: function(response) {
					console.log(response);
					$(".profile_info .savebtn_top").hide();
					$(".profile_info .social .name").show();
					$(".profile_info .social .edittitle").hide();
					$(".profile_info .social .name").text(obj.first_name + " " + obj.last_name);
					if ($(".editlinkedin input").val() !== "") {
						$(".linkedin span").html("<a href=" + obj.linkid_url + ">" + obj.linkid_url + "</a>");
					} else {
						$(".linkedin span").text(obj.linkid_url);
					}
					$(".profile_info .linkedin span, .profile_info .linkedin >img").show();
					$(".profile_info .linkedin .editlinkedin").hide();
					if ($(".editfacebook input").val() !== "") {
						$(".facebook span").html("<a href=" + obj.fb_url + ">" + obj.fb_url + "</a>");
					} else {
						$(".facebook span").text(obj.fb_url);
					}
					$(".profile_info .facebook span, .profile_info .facebook >img").show();
					$(".profile_info .facebook .editfacebook").hide();
					if ($(".editgithub input").val() !== "") {
						$(".github span").html("<a href=" + obj.git_url + ">" + obj.git_url + "</a>");
					} else {
						$(".github span").text(obj.git_url);
					}
					$(".profile_info .github span, .profile_info .github >img").show();
					$(".profile_info .github .editgithub").hide();
				}
			});

    } else {
			alert("Please enter your first or last name!");
		}
   
  });

   $(".profile_text #paypal_add").click(function() {
  	$(this).hide();
  	$(".profile_text .edit_paypal").fadeIn("fast");
  	$(".profile_text .edit_paypal input").val($(".profile_text__paypal").text());
  });

  $(".profile_text #paypalchange").click(function() {
		if (isEmail($(".profile_text .edit_paypal input").val()) || $(".profile_text .edit_paypal input").val() == "") {
			$(".profile_text__paypal").text($(".profile_text .edit_paypal input").val());
			$(".profile_text .edit_paypal").hide();
			$(".profile_text #paypal_add").show();
			var obj = {
				paypal_email : $(".profile_text .edit_paypal input").val()
			}
			$.ajax({
				type: "PUT",
				url: "/api/user/update",
				data: obj,
				datatype: "json",
				success: function(response) {
					console.log(response);
				}
			});
		} else {
			alert("Enter your paypal_email correctly!");
		}
  });
  $(".profile_text .edit_paypal .close").click(function() {
  	$(".profile_text .edit_paypal").hide();
  	$(".profile_text #paypal_add").fadeIn("fast");
  });

  $(".profile_text #descr_add").click(function() {
  	$(this).hide();
  	$(".profile_text .edit_description").fadeIn("fast");
  	$(".profile_text .edit_description textarea").val($(".profile_text__description").text());
  });

  $(".profile_text #descrchange").click(function() {
		$(".profile_text__description").text($(".profile_text .edit_description textarea").val());
		$(".profile_text .edit_description").hide();
		$(".profile_text #descr_add").show();
		var obj = {
			description: $(".profile_text .edit_description textarea").val()
		}
		$.ajax({
			type: "PUT",
			url: "/api/user/update",
			data: obj,
			datatype: "json",
			success: function(response) {
				console.log(response);
			}
		});
  });

  $(".profile_text .edit_description .close").click(function() {
  	$(".profile_text .edit_description").hide();
  	$(".profile_text #descr_add").fadeIn("fast");
  });


  ////EDIT PORTFOLIO


  ///JOB

  var now = new Date;
	console.log(now)
  $(".one_more button").click(function() {
  	var obj = "<div class='work_block'>\
                  <h2></h2>\
                  <p><span class='fromspan'></span><span class='defis'> - </span><span class='tospan'></span></p>\
                  <p class='mixed'></p>\
                  <div class='edit_profile' title='Edit job'><img src='http://jobgrouper.com/img/Profile/edit_pencil.png' alt='alt'></div>\
                  <div class='work_edit'>\
                      <input type='text' class='longinput jobtitle' placeholder='Job title'>\
                      <div class='date login'>\
                          <input type='text' class='from' maxlength='10' readonly placeholder='From'>\
                          <input type='text' class='to' maxlength='10' readonly placeholder='To'>\
                          <input type='checkbox' id='present'/>\
	  						<label for='present' class='present'>present</label>\
                      </div>\
                      <textarea class='longinput addinfo' placeholder='Additional information'></textarea>\
                      <div class='work_edit__btn'>\
                          <button data-proid='1' class='workchange'>Save</button><button class='delete'>Delete job</button><button class='close'><img src='http://jobgrouper.com/img/Profile/cancel.png' alt='alt'></button><button class='close_edit'><img src='http://jobgrouper.com/img/Profile/cancel.png') alt='alt'></button>\
                      </div>\
                  </div>\
              </div>"
  	$(".work_block__wrapper").append(obj);
  	$(".work_block__wrapper .work_block:last-child .work_edit").show();
  	$(".work_block__wrapper .work_block:last-child .edit_profile").hide();
  	$(".work_block__wrapper .work_block:last-child .work_edit").find(".workchange").attr("data-proid", 1);
  	$(this).parent().hide();

  	$(".from").pickmeup({
		  format  : 'm-d-Y',
		  hide_on_select: true,
		});

		$(".to").pickmeup({
		format  : 'm-d-Y',
		   hide_on_select: true,
		   render : function (date) {
        if (date <= new Date($(this).prev().val())) {
            return {disabled : true, class_name : 'date-in-past'};
        }
        return {};
    	}
		});
  	$(".work_block__wrapper .work_block .workchange").click(function() {
	  	if ($(this).parents(".work_edit").find(".jobtitle").val().length > 0 && $(this).parents(".work_edit").find(".from").val().length > 0 && $(this).parents(".work_edit").find(".to").val().length > 0) {
	  		$(this).parents(".work_block").find("h2").text($(this).parents(".work_edit").find(".jobtitle").val());
	  		$(this).parents(".work_block").find("p .defis").show();
	  		$(this).parents(".work_block").find("p .fromspan").text($(this).parents(".work_edit").find(".from").val());
  			$(this).parents(".work_block").find("p .tospan").text($(this).parents(".work_edit").find(".to").val());
	  		$(this).parents(".work_block").find(".mixed").text($(this).parents(".work_edit").find(".addinfo").val());
	  		$(this).parents(".work_block").find(".work_edit").hide();
	  		$(this).parents(".work_block").fadeIn("fast");
	  		$(".profile_portfolio .block .one_more").show();
	  		$(this).parents(".work_block").find(".edit_profile").show();
	  		var obj = {
	  			title: $(this).parents(".work_edit").find(".jobtitle").val(),
	  			date_from: $(this).parents(".work_edit").find(".from").val(),
	  			date_to: $(this).parents(".work_edit").find(".to").val(),
	  			additional_info: $(this).parents(".work_edit").find(".addinfo").val()
	  		}
	  		console.log(obj);
	  		if($(this).attr("data-proid") == 1) {
	  			$.ajax({
						type: "POST",
						url: "/api/experience",
						data: obj,
						datatype: "json",
						success: function(response) {
							console.log(response);
							for (var i = 0; i< $(".work_block .work_edit").length; i++) {
								console.log($(".work_block .work_edit").eq(i).attr("data-id"));
								if ($(".work_block .work_edit").eq(i).attr("data-id") == undefined) {
									$(".work_block:last-child").find(".work_edit").attr("data-id", response.id);
								}
							}
						}
					});
	  		}
	  	} else {
	  		alert("Please fill the fields!!! Additional information is optional");
	  	}
  	});
  	$(".work_block__wrapper .work_block:last-child .close").click(function() {
  		$(".work_block__wrapper .work_block:last-child").remove();
  		$(".profile_portfolio .block .one_more").show();
  	});
  		
  });



$(".work_block__wrapper").on("click", ".delete", function() {
	if (confirm("You are trying to delete job information. Are you sure?")) {
		$(this).parents(".work_block").remove();
		$.ajax({
			type: "DELETE",
			url: "/api/experience/" + +$(this).parents(".work_edit").attr("data-id"),
				datatype: "json",
				success: function(response) {
					console.log(response);
				}
		});
	}
});

$(".work_block__wrapper").on("click", ".edit_profile", function() {
	$(this).parent().find("h2").hide();
	$(this).parent().find("p").hide();
	$(this).parent().find(".close").hide();
	$(this).parent().find(".close_edit").show();
	$(this).parent().find(".mixed").hide();
	$(this).parent().find(".work_edit").fadeIn("fast");
	$(this).parent().find(".delete").show();
	$(this).parent().find(".workchange").attr("data-proid", 2);
	$(".from").pickmeup({
		format  : 'm-d-Y',
		   hide_on_select: true,
		});

	$(".to").pickmeup({
		format  : 'm-d-Y',
		hide_on_select: true,
		render : function (date) {
			if (date <= new Date($(this).prev().val())) {
				return {disabled : true, class_name : 'date-in-past'};
			}
			return {};
		}
	});
	$(this).parent().find(".work_edit").find(".jobtitle").val($(this).parent().find("h2").text());
	$(this).parent().find(".work_edit").find(".from").val($(this).parent().find(".fromspan").text());
	$(this).parent().find(".work_edit").find(".to").val($(this).parent().find(".tospan").text());
	$(this).parent().find(".work_edit").find(".addinfo").val($(this).parent().find(".mixed").text());
	console.log($(this).parent().find("h2").text());
	$(this).parent().find(".workchange").click(function() {
		if ($(this).parents(".work_edit").find(".jobtitle").val().length > 0 && $(this).parents(".work_edit").find(".from").val().length > 0 && $(this).parents(".work_edit").find(".to").val().length > 0) {
			$(this).parents(".work_block").find("h2").fadeIn("fast");
			$(this).parents(".work_block").find("p").fadeIn("fast");
			$(this).parents(".work_block").find(".work_edit").hide();
			$(this).parents(".work_block").find(".mixed").fadeIn("fast");
			$(this).parents(".work_block").find(".delete").hide();
			$(this).parents(".work_block").find("h2").text($(this).parents(".work_edit").find(".jobtitle").val());
  		$(this).parents(".work_block").find("p .fromspan").text($(this).parents(".work_edit").find(".from").val());
  		$(this).parents(".work_block").find("p .tospan").text($(this).parents(".work_edit").find(".to").val());
  		$(this).parents(".work_block").find(".mixed").text($(this).parents(".work_edit").find(".addinfo").val());
  		var obj = {
  			id: +$(this).parents(".work_edit").attr("data-id"),
  			title: $(this).parents(".work_edit").find(".jobtitle").val(),
  			date_from: $(this).parents(".work_edit").find(".from").val(),
  			date_to: $(this).parents(".work_edit").find(".to").val(),
  			additional_info: $(this).parents(".work_edit").find(".addinfo").val()
  		}
  		console.log(obj);
  		$.ajax({
				type: "PUT",
				url: "/api/experience/" + +$(this).parents(".work_edit").attr("data-id"),
				data: obj,
				datatype: "json",
				success: function(response) {
					console.log(response);
				}
			});
		}
	});
	$(this).parent().find(".close_edit").click(function() {
		$(this).parents(".work_edit").hide();
		$(this).parents(".work_block").find("h2").fadeIn("fast");
		$(this).parents(".work_block").find("p").fadeIn("fast");
		$(this).parents(".work_block").find(".mixed").fadeIn("fast");
		$(this).parents(".work_block").find(".delete").hide();
	});
});
	

	///EDUCATION

	//////////

$(".one_more2 button").click(function() {
  	var obj = "<div class='education_block'>\
                  <h2></h2>\
                  <p><span class='fromspan'></span><span class='defis'> - </span><span class='tospan'></span></p>\
                  <p class='mixed'></p>\
                  <div class='edit_profile' title='Edit education'><img src='http://jobgrouper.com/img/Profile/edit_pencil.png' alt='alt'></div>\
                  <div class='work_edit'>\
                      <input type='text' class='longinput jobtitle' placeholder='Education title'>\
                      <div class='date login'>\
                          <input type='text' class='from' maxlength='10' readonly placeholder='From'>\
                          <input type='text' class='to' maxlength='10' readonly placeholder='To'>\
                          <input type='checkbox' id='present'/>\
	  					<label for='present' class='present'>present</label>\
                      </div>\
                      <textarea class='longinput addinfo' placeholder='Additional information'></textarea>\
                      <div class='work_edit__btn'>\
                          <button data-proid='1' class='workchange'>Save</button><button class='delete'>Delete job</button><button class='close'><img src='http://jobgrouper.com/img/Profile/cancel.png' alt='alt'></button><button class='close_edit'><img src='http://jobgrouper.com/img/Profile/cancel.png') alt='alt'></button>\
                      </div>\
                  </div>\
              </div>"
  	$(".education_block__wrapper").append(obj);
  	$(".education_block__wrapper .education_block:last-child .work_edit").show();
  	$(".education_block__wrapper .education_block:last-child .edit_profile").hide();
  	$(".education_block__wrapper .education_block:last-child .work_edit").find(".workchange").attr("data-proid", 1);
  	$(this).parent().hide();
  	$(".from").pickmeup({
		format  : 'm-d-Y',
		  hide_on_select: true,
		});

	$(".to").pickmeup({
		format  : 'm-d-Y',
		hide_on_select: true,
		render : function (date) {
			if (date <= new Date($(this).prev().val())) {
				return {disabled : true, class_name : 'date-in-past'};
			}
			return {};
		}
	});
  	$(".education_block__wrapper .education_block .workchange").click(function() {
	  	if ($(this).parents(".work_edit").find(".jobtitle").val().length > 0 && $(this).parents(".work_edit").find(".from").val().length > 0 && $(this).parents(".work_edit").find(".to").val().length > 0) {
	  		$(this).parents(".education_block").find("h2").text($(this).parents(".work_edit").find(".jobtitle").val());
	  		$(this).parents(".education_block").find("p .defis").show();
	  		$(this).parents(".education_block").find("p .fromspan").text($(this).parents(".work_edit").find(".from").val());
  			$(this).parents(".education_block").find("p .tospan").text($(this).parents(".work_edit").find(".to").val());
	  		$(this).parents(".education_block").find(".mixed").text($(this).parents(".work_edit").find(".addinfo").val());
	  		$(this).parents(".education_block").find(".work_edit").hide();
	  		$(this).parents(".education_block").fadeIn("fast");
	  		$(".profile_portfolio .block .one_more2").show();
	  		$(this).parents(".education_block").find(".edit_profile").show();
	  		var obj = {
	  			title: $(this).parents(".work_edit").find(".jobtitle").val(),
	  			date_from: $(this).parents(".work_edit").find(".from").val(),
	  			date_to: $(this).parents(".work_edit").find(".to").val(),
	  			additional_info: $(this).parents(".work_edit").find(".addinfo").val()
	  		}
	  		console.log(obj);
	  		if($(this).attr("data-proid") == 1) {
	  			$.ajax({
						type: "POST",
						url: "/api/education",
						data: obj,
						datatype: "json",
						success: function(response) {
							console.log(response);
							for (var i = 0; i< $(".education_block .work_edit").length; i++) {
								console.log($(".education_block .work_edit").eq(i).attr("data-id"));
								if ($(".education_block .work_edit").eq(i).attr("data-id") == undefined) {
									$(".education_block:last-child").find(".work_edit").attr("data-id", response.id);
								}
							}
						}
					});
	  		}
	  	} else {
	  		alert("Please fill the fields!!! Additional information is optional");
	  	}
  	});
  	$(".education_block__wrapper .education_block:last-child .close").click(function() {
  		$(".education_block__wrapper .education_block:last-child").remove();
  		$(".profile_portfolio .block .one_more2").show();
  	});
  		
  });



$(".education_block__wrapper").on("click", ".delete", function() {
	if (confirm("You are trying to delete job information. Are you sure?")) {
		$(this).parents(".education_block").remove();
		$.ajax({
			type: "DELETE",
			url: "/api/education/" + +$(this).parents(".work_edit").attr("data-id"),
				datatype: "json",
				success: function(response) {
					console.log(response);
				}
		});
	}
});

$(".education_block__wrapper").on("click", ".edit_profile", function() {
	$(this).parent().find("h2").hide();
	$(this).parent().find("p").hide();
	$(this).parent().find(".close").hide();
	$(this).parent().find(".close_edit").show();
	$(this).parent().find(".mixed").hide();
	$(this).parent().find(".work_edit").fadeIn("fast");
	$(this).parent().find(".delete").show();
	$(this).parent().find(".workchange").attr("data-proid", 2);
	$(".from").pickmeup({
		format  : 'm-d-Y',
		  hide_on_select: true,
		});

	$(".to").pickmeup({
		format  : 'm-d-Y',
		hide_on_select: true,
		render : function (date) {
			if (date <= new Date($(this).prev().val())) {
				return {disabled : true, class_name : 'date-in-past'};
			}
			return {};
		}
	});
	$(this).parent().find(".work_edit").find(".jobtitle").val($(this).parent().find("h2").text());
	$(this).parent().find(".work_edit").find(".from").val($(this).parent().find(".fromspan").text());
	$(this).parent().find(".work_edit").find(".to").val($(this).parent().find(".tospan").text());
	$(this).parent().find(".work_edit").find(".addinfo").val($(this).parent().find(".mixed").text());
	console.log($(this).parent().find("h2").text());
	$(this).parent().find(".workchange").click(function() {
		if ($(this).parents(".work_edit").find(".jobtitle").val().length > 0 && $(this).parents(".work_edit").find(".from").val().length > 0 && $(this).parents(".work_edit").find(".to").val().length > 0) {
			$(this).parents(".education_block").find("h2").fadeIn("fast");
			$(this).parents(".education_block").find("p").fadeIn("fast");
			$(this).parents(".education_block").find(".work_edit").hide();
			$(this).parents(".education_block").find(".mixed").fadeIn("fast");
			$(this).parents(".education_block").find(".delete").hide();
			$(this).parents(".education_block").find("h2").text($(this).parents(".work_edit").find(".jobtitle").val());
  		$(this).parents(".education_block").find("p .fromspan").text($(this).parents(".work_edit").find(".from").val());
  		$(this).parents(".education_block").find("p .tospan").text($(this).parents(".work_edit").find(".to").val());
  		$(this).parents(".education_block").find(".mixed").text($(this).parents(".work_edit").find(".addinfo").val());
  		var obj = {
  			id: +$(this).parents(".work_edit").attr("data-id"),
  			title: $(this).parents(".work_edit").find(".jobtitle").val(),
  			date_from: $(this).parents(".work_edit").find(".from").val(),
  			date_to: $(this).parents(".work_edit").find(".to").val(),
  			additional_info: $(this).parents(".work_edit").find(".addinfo").val()
  		}
  		console.log(obj);
  		$.ajax({
				type: "PUT",
				url: "/api/education/" + +$(this).parents(".work_edit").attr("data-id"),
				data: obj,
				datatype: "json",
				success: function(response) {
					console.log(response);
				}
			});
		}
	});
	$(this).parent().find(".close_edit").click(function() {
		$(this).parents(".work_edit").hide();
		$(this).parents(".education_block").find("h2").fadeIn("fast");
		$(this).parents(".education_block").find("p").fadeIn("fast");
		$(this).parents(".education_block").find(".mixed").fadeIn("fast");
		$(this).parents(".education_block").find(".delete").hide();
	});
});


	/////////



	///SKILLS


	if ($(".profile_portfolio .block .solo span").length > 0) {
		$(".skills_block__wrapper .skills_block:last-child .edit_profile").show();
	}
	$(".one_more3 button").click(function() {
  	var obj = "<div class='skills_block'>\
                  <div class='work_edit'>\
                      <input type='text' class='longinput jobtitle' placeholder='Skill title'>\
                      <div class='work_edit__btn'>\
                          <button data-proid='1' class='workchange'>Save</button><button class='delete'>Delete education</button><button class='close'><img src='http://jobgrouper.com/img/Profile/cancel.png' alt='alt'></button><button class='close_edit'><img src='http://jobgrouper.com/img/Profile/cancel.png') alt='alt'></button>\
                      </div>\
                  </div>\
              </div>"
  		$(".skills_block__wrapper").append(obj);
  	$(".skills_block__wrapper .skills_block:last-child .work_edit").show();
  	$(".skills_block__wrapper .skills_block:last-child .work_edit").find(".workchange").attr("data-proid", 1);
  	$(this).parent().hide();
  	$(".skills_block__wrapper .skills_block .workchange").click(function() {
	  	if ($(this).parents(".work_edit").find(".jobtitle").val().length > 0 ) {
	  		$(".skills_block__wrapper .solo").append("<span>"+ $(this).parents(".work_edit").find(".jobtitle").val()+"</span><button class='close'><img src='http://jobgrouper.com/img/Profile/cancel.png' alt='alt'></button>");
	  		$(this).parents(".skills_block").find(".work_edit").hide();
	  		$(this).parents(".skills_block").fadeIn("fast");
	  		$(".profile_portfolio .block .one_more3").show();
	  		$(this).parents(".skills_block").find(".edit_profile").show();
	  		var obj = {
	  			title: $(this).parents(".work_edit").find(".jobtitle").val(),
	  		}
	  		if($(this).attr("data-proid") == 1) {
	  			$.ajax({
						type: "POST",
						url: "/api/skill",
						data: obj,
						datatype: "json",
						success: function(response) {
							console.log(response);
							// for (var i = 0; i< $(".profile_portfolio .block .solo span").length; i++) {
							// 	if ($(".education_block .work_edit").eq(i).attr("data-id") == undefined) {
							// 		$(".education_block:last-child").find(".work_edit").attr("data-id", response.id);
							// 	}
							// }
							$(".profile_portfolio .block .solo span:not([data-id])").attr("data-id", response.id);
						}
					});
  			}
	  	} else {
	  		alert("Please fill the field!!!");
	  	}
  	});
  	$(".skills_block__wrapper .skills_block:last-child .close").click(function() {
  		$(".skills_block__wrapper .skills_block:last-child").remove();
  		$(".profile_portfolio .block .one_more3").show();
  	});
  	
  });

$(".skill").on("click", ".mixed .close", function() {
	if(confirm("Delete this skill?")) {
		console.log($(this).prev().attr("data-id"));
		var deleteElement = $(this).prev();
		$.ajax({
			type: "DELETE",
			url: "/api/skill/" + +$(this).prev().attr("data-id"),
			datatype: "json",
			success: function(response) {
				console.log(response);
				alert("Skill has been successfully deleted!!!");
				deleteElement.remove().end().remove();
			}
		});
	}
	
});

// $(".profile_portfolio .skill .edit_profile").click(function() {
//   		var obj;
//   		for (var i =1; i < $(".profile_portfolio .block .solo span").length+1; i++ ) {
//   			obj = $(".profile_portfolio .block .solo span:nth-child(" + i +")").text();
//   			$(".skill_edit").find(".work_edit__btn").before("<input class='longinput' id='"+obj+"' value='" + obj + "'>");
//   		}
//   		$(".skills_block__wrapper").hide();
//   		$(".one_more3").hide();
//   		$(".profile_portfolio .block .solo span")
//   		$(this).parent().find(".close").hide();
//   		$(this).parent().find(".close_edit").show();
//   		$(this).parent().find(".mixed").hide();
//   		$(".skill_edit").fadeIn("fast");
//   		$(this).parent().find(".delete").show();
//   		$(".skill_edit").find(".workchange").click(function() {

// 				$('.skill_edit input').each(function(){
// 					var id = $(this).attr("id");
// 					var val = $(this).val();
// 					if (val == "") {
// 						alert("Skill has been removed!");
// 						$('.'+id).remove();
// 						if ($(".profile_portfolio .block .solo span").length == 0) {
// 							$(".profile_portfolio .block .skills_block .edit_profile").hide();
// 						}
// 					} else {
// 						$('.'+id).text(val);
// 						$('.'+id).attr("class", val);
// 					}
// 					console.log(id);
					
// 				});
// 				$(this).parents(".skill_edit").find(".longinput").remove();
// 				$(".skill_edit").hide();
// 				$(".skills_block__wrapper").fadeIn("fast");
//   			$(".one_more3").fadeIn("fast");


//   			// $(this).parents(".skills_block").find(".mixed").fadeIn("fast");
//   			// $(this).parents(".skills_block").find(".delete").hide();
//   		});
//   		$(".skill_edit .close").click(function() {
// 				$(this).parents(".skill_edit").find(".longinput").remove();
// 				$(this).parents(".skill_edit").hide();
// 				$(".skills_block__wrapper").fadeIn("fast");
//   			$(".one_more3").fadeIn("fast");
// 			});
//   		$(this).parent().find(".close_edit").click(function() {
//   			$(this).parents(".skill_edit").hide();
//   			$(this).parents(".skill").find(".skills_block__wrapper").fadeIn("fast");
//   			$(this).parents(".skill").find(".skills_block__wrapper .solo").fadeIn("fast");
//   			$(".one_more3").fadeIn("fast");
//   		});
//   		$(this).parent().find(".delete").click(function() {
//   			if (confirm("You are trying to delete job information. Are you sure?")) {
// 					$(this).parents(".skills_block").remove()
//   			}
//   		});
//   	});


	////ADDITIONAL


	////////
$(".one_more4 button").click(function() {
	var obj = "<div class='additional_block'>\
                  <h2></h2>\
                  <p></p>\
                  <p class='mixed'></p>\
                  <div class='edit_profile' title='Edit information'><img src='http://jobgrouper.com/img/Profile/edit_pencil.png' alt='alt'></div>\
                  <div class='work_edit'>\
                      <input type='text' class='longinput jobtitle' placeholder='Title'>\
                      <input type='text' class='longinput addinfo' placeholder='Information'>\
                      <div class='work_edit__btn'>\
                          <button data-proid='1' class='workchange'>Save</button><button class='delete'>Delete information</button><button class='close'><img src='http://jobgrouper.com/img/Profile/cancel.png' alt='alt'></button><button class='close_edit'><img src='http://jobgrouper.com/img/Profile/cancel.png') alt='alt'></button>\
                      </div>\
                  </div>\
              </div>"
  	$(".additional_block__wrapper").append(obj);
  	$(".additional_block__wrapper .additional_block:last-child .work_edit").show();
  	$(".additional_block__wrapper .additional_block:last-child .edit_profile").hide();
  	$(".additional_block__wrapper .additional_block:last-child .work_edit").find(".workchange").attr("data-proid", 1);
  	$(this).parent().hide();
  	$(".additional_block__wrapper .additional_block .workchange").click(function() {
	  	if ($(this).parents(".work_edit").find(".jobtitle").val().length > 0 && $(this).parents(".work_edit").find(".addinfo").val().length > 0 ) {
	  		$(this).parents(".additional_block").find("h2").text($(this).parents(".work_edit").find(".jobtitle").val());
	  		$(this).parents(".additional_block").find("p .defis").show();
	  		$(this).parents(".additional_block").find("p .fromspan").text($(this).parents(".work_edit").find(".from").val());
  			$(this).parents(".additional_block").find("p .tospan").text($(this).parents(".work_edit").find(".to").val());
	  		$(this).parents(".additional_block").find(".mixed").text($(this).parents(".work_edit").find(".addinfo").val());
	  		$(this).parents(".additional_block").find(".work_edit").hide();
	  		$(this).parents(".additional_block").fadeIn("fast");
	  		$(".profile_portfolio .block .one_more4").show();
	  		$(this).parents(".additional_block").find(".edit_profile").show();
	  		var obj = {
	  			title: $(this).parents(".work_edit").find(".jobtitle").val(),
	  			additional_info: $(this).parents(".work_edit").find(".addinfo").val()
	  		}
	  		console.log(obj);
	  		if($(this).attr("data-proid") == 1) {
	  			$.ajax({
						type: "POST",
						url: "/api/addition",
						data: obj,
						datatype: "json",
						success: function(response) {
							console.log(response);
							for (var i = 0; i< $(".additional_block .work_edit").length; i++) {
								console.log($(".additional .work_edit").eq(i).attr("data-id"));
								if ($(".additional_block .work_edit").eq(i).attr("data-id") == undefined) {
									$(".additional_block:last-child").find(".work_edit").attr("data-id", response.id);
								}
							}
						}
					});
	  		}
	  	} else {
	  		alert("Please fill the fields!!! Additional information is optional");
	  	}
  	});
  	$(".additional_block__wrapper .additional_block:last-child .close").click(function() {
  		$(".additional_block__wrapper .additional_block:last-child").remove();
  		$(".profile_portfolio .block .one_more4").show();
  	});
  		
  });



$(".additional_block__wrapper").on("click", ".delete", function() {
	if (confirm("You are trying to delete job information. Are you sure?")) {
		$(this).parents(".additional_block").remove();
		$.ajax({
			type: "DELETE",
			url: "/api/addition/" + +$(this).parents(".work_edit").attr("data-id"),
				datatype: "json",
				success: function(response) {
					console.log(response);
				}
		});
	}
});

$(".additional_block__wrapper").on("click", ".edit_profile", function() {
	$(this).parent().find("h2").hide();
	$(this).parent().find("p").hide();
	$(this).parent().find(".close").hide();
	$(this).parent().find(".close_edit").show();
	$(this).parent().find(".mixed").hide();
	$(this).parent().find(".work_edit").fadeIn("fast");
	$(this).parent().find(".delete").show();
	$(this).parent().find(".workchange").attr("data-proid", 2);
	$(this).parent().find(".work_edit").find(".jobtitle").val($(this).parent().find("h2").text());
	$(this).parent().find(".work_edit").find(".from").val($(this).parent().find(".fromspan").text());
	$(this).parent().find(".work_edit").find(".to").val($(this).parent().find(".tospan").text());
	$(this).parent().find(".work_edit").find(".addinfo").val($(this).parent().find(".mixed").text());
	console.log($(this).parent().find("h2").text());
	$(this).parent().find(".workchange").click(function() {
		if ($(this).parents(".work_edit").find(".jobtitle").val().length > 0 && $(this).parents(".work_edit").find(".addinfo").val().length > 0 ) {
			$(this).parents(".additional_block").find("h2").fadeIn("fast");
			$(this).parents(".additional_block").find("p").fadeIn("fast");
			$(this).parents(".additional_block").find(".work_edit").hide();
			$(this).parents(".additional_block").find(".mixed").fadeIn("fast");
			$(this).parents(".additional_block").find(".delete").hide();
			$(this).parents(".additional_block").find("h2").text($(this).parents(".work_edit").find(".jobtitle").val());
  		$(this).parents(".additional_block").find("p .fromspan").text($(this).parents(".work_edit").find(".from").val());
  		$(this).parents(".additional_block").find("p .tospan").text($(this).parents(".work_edit").find(".to").val());
  		$(this).parents(".additional_block").find(".mixed").text($(this).parents(".work_edit").find(".addinfo").val());
  		var obj = {
  			id: +$(this).parents(".work_edit").attr("data-id"),
  			title: $(this).parents(".work_edit").find(".jobtitle").val(),
  			additional_info: $(this).parents(".work_edit").find(".addinfo").val()
  		}
  		console.log(obj);
  		$.ajax({
				type: "PUT",
				url: "/api/addition/" + +$(this).parents(".work_edit").attr("data-id"),
				data: obj,
				datatype: "json",
				success: function(response) {
					console.log(response);
				}
			});
		}
	});
	$(this).parent().find(".close_edit").click(function() {
		$(this).parents(".work_edit").hide();
		$(this).parents(".additional_block").find("h2").fadeIn("fast");
		$(this).parents(".additional_block").find("p").fadeIn("fast");
		$(this).parents(".additional_block").find(".mixed").fadeIn("fast");
		$(this).parents(".additional_block").find(".delete").hide();
	});
});



	///////

  ////PROFILE EDIT FINISH


//////////////////// VIEW PAGE

$(".view_sidebar .buttons .apply").on("click", function() {
	var jobId = window.location.href.split("/");
	var job_id = jobId[jobId.length - 1];
	if (confirm("Is your profile filled out completely? Your profile is the sole basis for which you are selected for jobs at JobGrouper.")) {
		$.ajax({
			type: "POST",
			url:  "/api/employeeRequest/" + job_id,
			dataType: "json",
			success: function(response) {
				console.log(response);
				if (response.status == 0) {
					$(".view_sidebar .buttons").hide();
					$(".view_sidebar .statebuttons .pending").show();
				}
			}
		});
	}
});


////FORGOT PASSWORD
$(".forgot_only #email").focusout(function() {
	if ($("#email").val().trim().length > 0) {
		var obj = {
			email: $("#email").val(),
		}
		$.ajax({
			type: "POST",
			url: "/api/checkEmailFree",
			data: obj,
			datatype: "json",
			success: function(response) {
				console.log(response);
				if(response.status == 0) {
					$(".forgot_only .invalid_login").text("Invalid email address");
				} else {
					$(".forgot_only .invalid_login").html("");
				}
			}
		});
	}
});
$(".forgot_only button").on("click", function(event) {
	var self = $(this);
	if ($("#email").val().trim().length > 0) {
		var obj = {
			email: $("#email").val(),
		}
		event.preventDefault();
		$.ajax({
			type: "POST",
			url: "/api/checkEmailFree",
			data: obj,
			datatype: "json",
			success: function(response) {
				console.log(response);
				if(response.status == 0) {
					$(".forgot_only .invalid_login").text("Invalid email address");
				} else {
					$(".forgot_only .invalid_login").html("");
					self.parent().submit();
				}
			}
		});
	} else {
		alert("Fill the field correctly!");
		event.preventDefault();
	}
});


///////// MY JOBS


$(".myjobs .workers_item .cancelbtn").on("click", function() {
	var order_id = $(this).attr("data-order_id");
	var self = $(this);
	$.ajax({
		type: "POST",
		url: "/api/order/close/" + order_id,
		dataType: "json",
		success: function(response) {
			console.log(response);
			if (response.error == false) {
				console.log(123);
				self.parent().hide();
				alert("You have declined your participation in this project. Your request has been sent successfully to the administrator!");
			} else {
				alert(response.info);
			}
		}
	});
});

////// CARD INFO


// function isNumeric(n) {
//   return !isNaN(parseFloat(n)) && isFinite(n);
// }

// setInterval(function() {
// 	var val = $(".creditcard_info__form #cardnumber").val();
// 	// if(isNumeric(val)) { 
// 	// 	console.log('number');
// 	var onPress;
// 		if (val.length == 16) {
// 			$(".creditcard_info__form #cardnumber").val(val.slice(0,4) + " " + val.slice(4,8) + " " + val.slice(8,12) + " " + val.slice(12,16));
// 		}
// 		else if(val.length == 12) {
// 			$(".creditcard_info__form #cardnumber").val(val.slice(0,4) + " " + val.slice(4,8) + " " + val.slice(8,12) + " ");
// 		}
// 		else if(val.length == 8) {
// 			onPress += val.slice(4,8) + " ");
// 		}
// 		else if(val.length == 4) {
// 			onPress = val.slice(0,4) + " ");
// 		}
// 	// } 
// 	// else { console.log('not number'); }
// }, 100);





$(".creditcard_info__form button").on("click", function(event) {
	event.preventDefault();
	if ($("#firstname").val().trim().length > 0 && $("#lastname").val().trim().length > 0 && $("#cardnumber").val().trim().length == 16 && $("#cvv").val().trim().length == 3 && $("#endmonth").val() != 0 && $("#endyear").val() !=0 ) {
		// var self = $(this);
		$(this).parent().parent().submit();
		// var orderId = window.location.href.split("/");
		// var realId = orderId[orderId.length - 1];
		// var obj = {
		// 	first_name: $("#firstname").val(),
		// 	last_name: $("#lastname").val(),
		// 	card_number: $("#cardnumber").val(),
		// 	end_month: $("#endmonth").val(),
		// 	end_year: $("#endyear").val(),
		// 	cvv: $("#cvv").val(),
		// 	order_id: realId
		// }
		// console.log(obj);
		// $.ajax({
		// 	type: "POST",
		// 	url: "/api/order/purchase_via_stripe",
		// 	data: obj,
		// 	datatype: "json",
		// 	success: function(response) {
		// 		if(response.error == true) {
		// 			$(".creditcard_info__form .invalid_login").text(response.info);
		// 		} else {
		// 			$(".allforms").hide();
		// 			$(".creditcard_info .success").show();
		// 		}
		// 	}
		// })
	} else {
		alert("Fill all the fields correctly!");
		event.preventDefault();
	}
});

$(".creditcard_info .allforms .card #paypal").on("change", function() {
	$("#visaform").hide();
	$("#paypalform").show();
});

$(".creditcard_info .allforms .card #visa").on("change", function() {
	$("#visaform").show();
	$("#paypalform").hide();
});

if ($(window).innerWidth() < 992) {
	$(document).click(function() {
		$(".name_hover").removeAttr("style");
	});

	$(".dark_header__line .name span").on("click", function(e) {
		e.stopPropagation();
		$(".name_hover").css({
			"opacity": 1,
    	"top": "55px",
    	"marginTop": 0
		});
	});
}

$(window).resize(function() {
	if ($(window).innerWidth() < 992) {
		$(".dark_header__line .name span").off('mouseenter mouseleave');
	}
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
		aspectRatio: 1,
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


										// readURL(this, function(){
										    var obj = {
										    	image_hash: result.toDataURL()
										    }
										    console.log(obj);
										    $.ajax({
													type: "PUT",
													url: "//api/user/update",
													data: obj,
													datatype: "json",
													success: function(response) {
														console.log(response);
														$(".profile_info .img_wrapper img").attr("src", obj.image_hash);
													}
												});
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

    	$('#getCroppedCanvasModal').on('shown.bs.modal', function () {
    		console.log($("#previewwidth").text());
    		console.log($("#previewheight").text());
    		$image.cropper({
    			// aspectRatio: 1,
    			// minContainerWidth: 568,
    			// minContainerHeight: 300,
    			built: function () {
    				$image.cropper('setCanvasData', canvasData);
    				$image.cropper('setCropBoxData', cropBoxData);
    			}
    		});
    		// if(+$("#previewwidth").text() > 800) {
    		// 	$(".modal-body").height($(".cropper-bg").height()*(800/+$("#previewwidth").text()));
    		// } 
    		// if(+$("#previewwidth").text() <= 800) {
    		// 	$(".modal-body").height($(".cropper-bg").height());
    		// } 
    		// console.log($(".modal-body").height());
    	}).on('hidden.bs.modal', function () {
    		cropBoxData = $image.cropper('getCropBoxData');
    		canvasData = $image.cropper('getCanvasData');
    		// $(".here").hide();
      // 	$(this).remove();
      // 	$(".hide_btn").show();
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

} else {


  $('#file').change(function(){
    readURL(this, function() {
    	var obj = {
	    	image_hash: $(".profile_info .img_wrapper img").attr("src")
	    }
	    console.log(obj);
	    $.ajax({
				type: "PUT",
				url: "/api/user/update",
				data: obj,
				datatype: "json",
				success: function(response) {
					console.log(response);
					$(".profile_info .img_wrapper img").attr("src", obj.image_hash);
				}
			});
    })
  });

}

    


});
