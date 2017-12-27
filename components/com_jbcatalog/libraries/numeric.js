jQuery.noConflict();
  
  jQuery(function() {
	jQuery('div.slide1').each(function() {
		adf_id = jQuery(this).data('id');
		jQuery( this ).slider({
		  range: true,
		  min: jQuery(this).data('min'),
		  max: jQuery(this).data('max'),
		  values: [jQuery( "#amount_min_"+adf_id ).val(), jQuery( "#amount_max_"+adf_id ).val()],  //
		  slide: function( event, ui ) {
			inp_id = jQuery(this).data('id');
			jQuery( "#amount_min_"+inp_id ).val(ui.values[0]);
			jQuery( "#amount_max_"+inp_id ).val(ui.values[1]);
		  }
		});
		jQuery( "#amount_min_"+adf_id ).val( jQuery( this ).slider( "values", 0 ));
		jQuery( "#amount_max_"+adf_id ).val( jQuery( this ).slider( "values", 1 ));
	});
	
	jQuery(".numf").change(function(){
        adfid = jQuery(this).attr('id');
        adfnum = adfid.split('_');
        
        var value1=jQuery("#amount_min_"+adfnum[2]).val();
		var value2=jQuery("#amount_max_"+adfnum[2]).val();


		if(parseInt(value1) > parseInt(value2)){
			value1 = value2;
			jQuery("#amount_min_"+adfnum[2]).val(value1);
		}
		jQuery("#slider"+adfnum[2]).slider("values",0,value1);	
	});
	
	jQuery(".numf_m").change(function(){
        adfid = jQuery(this).attr('id');
        adfnum = adfid.split('_');
        
		var value1=jQuery("#amount_min_"+adfnum[2]).val();
		var value2=jQuery("#amount_max_"+adfnum[2]).val();
		
		 var max_val = jQuery("#slider"+adfnum[2]).data('max');
		
		if (value2 > max_val) { value2 = max_val; jQuery("#amount_max_"+adfnum[2]).val(max_val)}

		if(parseInt(value1) > parseInt(value2)){
			value2 = value1;
			jQuery("#amount_max_"+adfnum[2]).val(value2);
		}
		jQuery("#slider"+adfnum[2]).slider("values",1,value2);
	});
  
		jQuery(".numOnly").keypress(function (e) {
                   if (String.fromCharCode(e.keyCode).match(/[^0-9\.]/g)) return false;
         });		
  });