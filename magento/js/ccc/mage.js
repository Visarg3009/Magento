var Base = function() {

};
Base.prototype = {
    url : null,
    param : {},
    method : 'post',

    setUrl : function (url) {
        this.url = url;
        return this;
    },

    getUrl : function () {
        return this.url;
    },

    setMethod : function (method) {
        this.method = method;
        return this;
    },

    getMethod : function () {
        return this.method;
    },

    setParams : function (params) {
        this.params = params;
        return this;
    },

    getParams : function () {
        return this.params;
    },

    addParam : function (key,value) {
        this.params[key] = value;
        return this;
    },

    removeParam : function (key) {
        if (typeof this.params[key] != undefined) {
            delete this.params[key];
        }
        return this;
    },

    load : function () {
        var self = this;
        var request = jQuery.ajax({
            url : this.getUrl(),
            method : this.getMethod(),
            data : this.getParams(),

            success : function (response) {
                self.manageHtml(response);
            }
        });
    },

    manageHtml : function(response) {
        if (typeof response.element == 'undefined') {
            return false;
        }
        if (typeof response.element == 'object') {
            jQuery(response.element).each(function(i,element){
                jQuery(element.selector).html(element.html);
            })
        } else {
            jQuery(response.element.selector).html(response.element.html);
        }
    },

    setform : function(formId) {
        this.setUrl(jQuery(formId).attr('action'));
        this.setMethod(jQuery(formId).attr('method'));

        this.setParams(jQuery(formId).serializeArray());
        return this;
    },

    remove : function(obj) {
        jQuery(obj).parent().parent().parent().remove();
    },

    addOption : function() {
        newTr = jQuery('#newOption').children().children().clone();
        jQuery('#existingOption').prepend(newTr);
    },

    setCms : function() {
        var id = '#'+jQuery('form').attr('id');
        cmsContent = CKEDITOR.instances['cmsPage[content]'].getData();
        this.setParams(jQuery(id).serializeArray());
        this.setUrl(jQuery(id).attr('action'));
        this.setMethod(jQuery(id).attr('method'));
        
        jQuery.each(this.params,function(i,val) {
            if (val['name']=='cmsPage[content]') {
                val['value'] = cmsContent;
            }
        });
        return this;
    },

    uploadFile : function () {
        var formData = new FormData();
        var file = jQuery("#image")[0].files;
        formData.append('productFile', file[0]);
        this.setParams(formData);
        self = this;
        var request = jQuery.ajax({
            method : this.getMethod(),
            url : this.getUrl(),
            contentType : false,
            processData : false,
            data : this.getParams(),
            success : function (response) {
                self.manageHtml(response);
            }
        });
                
        return this;
    },
}