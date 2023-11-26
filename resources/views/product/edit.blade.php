@extends('adminlte::page')
@section('title',  $pageTitle)

@section('content_header')
    <h1>{{$pageTitle}}</h1>
@stop

@section('content')
<div class="container position-relative">
    <div class="row mb-5">
        <div class="col text-center">
            <button v-for="n in 3" class="btn btn-large btn-border">
                Step @{{n}}<br>
            </button>
        </div>
    </div>

    <div class="card" v-bind:class="isCurrentWizardStep(1)">
        <div class="card-header">Details</div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input class="form-control" v-bind:class="((wizardErrors[0] && !product.name) ? 'is-invalid':null )"type="text" v-model="product.name" name="name" required>
                            <div class="invalid-feedback">
                                Please provide a valid name.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Category</label>
                        <div class="col-sm-10">
                            <select class="form-control" v-model="product.category_id" name="category_id" v-bind:class="((wizardErrors[0] && !product.category_id) ? 'is-invalid':null )">
                                <option v-for="item in productCategories" :value="item.id">@{{ item.name }}</option>
                            </select>
                            <div class="invalid-feedback">
                                Please provide a category
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" type="text" v-model="product.description" name="description" v-bind:class="((wizardErrors[0] && !product.description) ? 'is-invalid':null )"></textarea>
                            <div class="invalid-feedback">
                                Please provide a description
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary" @click="changePage(2)">Next Step</button>
        </div>
    </div>

    <div class="card" v-bind:class="isCurrentWizardStep(2)">
        <div class="card-header">Images</div>
        <div class="card-body">
            <div id="uppy-uploader"></div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary" @click="changePage(1)">Go Back</button>&nbsp;
            <button class="btn btn-primary" @click="changePage(3)">Next Step</button>
        </div>
    </div>

    <div class="card" v-bind:class="isCurrentWizardStep(3)">
        <div class="card-header">Date</div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Date</label>
                        <div class="col-sm-10">
                            <input class="form-control" v-bind:class="((wizardErrors[2] && !product.datetime) ? 'is-invalid':null )" type="text" v-model="product.datetime" id="datetime" name="datetime" readonly required>
                            <div class="invalid-feedback">
                                Please provide a valid date.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary" @click="changePage(2)">Go Back</button>&nbsp;
            <button class="btn btn-success" @click="save()">Save</button>
        </div>
    </div>



</div>
@stop

@section('css')
<style>
.uppy-Dashboard-inner{width:100%}
</style>
@stop
@section('js')
<script>
var uppyImages;
const { createApp } = Vue
vueTable = createApp({
    data() {
        return {
            wizardErrors:[false, false, false],
            wizardStep:1,
            productCategories:{},
            product:{
                name:'',
                category_id:'',
                description:'',
                images:[],
                datetime:'',
            },
        }
    },
    methods : {
        save(){
            console.log(this.$data.product);
            if(!this.validateWizardStep()){
                return false;
            }

            // save data
            axios.post(`{{ route('api.products.store') }}`,this.$data.product,{
                headers: {
                    'Authorization': 'Bearer {{ $apiToken }}'
                }
            })
            .then(response => {
                // try to upload the images
                window.location.replace(`{{ route('products.index') }}`);
            });
        },
        showErrors(wizardStep){
            var stepIndex = wizardStep-1;
            this.$data.wizardErrors[stepIndex] = true;
            Toast.fire({
                icon: 'error',
                title: `Please complete step ${wizardStep}`
            });
            window.setTimeout(() => {
                this.$data.wizardErrors[stepIndex] = false;
            }, 3000)
            return false;
        },

        validateWizardStep(){
            for (let i = 1; i <= this.$data.wizardStep; i++) {
                if(
                    ((i==1) && (!this.$data.product.name || !this.$data.product.category_id || !this.$data.product.description )) ||
                    ((i==2) && (!uppyImages.getFiles().length)) ||
                    ((i==3) && (!this.$data.product.datetime))
                )
                    return this.showErrors(i);
            }
            return true;
        },
        isCurrentWizardStep(wizardStep){
            return (this.$data.wizardStep == wizardStep) ? null:'d-none';
        },
        changePage(wizardStep){
            if((wizardStep > this.$data.wizardStep) && !this.validateWizardStep())
                return false;
            this.$data.wizardStep = wizardStep;
        },

        getProduct(){
            vueTable.isTableLoading = true;
            axios.get(`{{ route('api.product.data') }}?page=${vueTable.current_page}&sort_by=${vueTable.filter.sort_by}&sort_order=${vueTable.filter.sort_order}&keyword=${vueTable.filter.keyword}&category=${vueTable.filter.category}`,{
                headers: {
                    'Authorization': 'Bearer {{ $apiToken }}'
                }
            })
            .then(response => {
                vueTable.links = response.data.links;
                vueTable.meta = response.data.meta;
                vueTable.products = response.data.data;
                vueTable.current_page = response.data.meta.current_page;
                vueTable.isTableLoading = false;
            });
        },

        getProductCategories(){
            axios.get(`{{ route('api.product-categories.data') }}`,{
                headers: {
                    'Authorization': 'Bearer {{ $apiToken }}'
                }
            })
            .then(response => {
                vueTable.productCategories = response.data.data;
            });
        },
    },
    mounted() {
        // initialize uploader
        uppyImages = new Uppy({
            restrictions:{
                maxFileSize:'2mb',
                allowedFileTypes:['.jpg', '.jpeg', '.png']
            },
            logger: debugLogger,
            id:'uppyImages'
        })
        .use(Dashboard, {
            id :'dashImages',
            inline: true,
            target: '#uppy-uploader',
            locale: {
                strings: {
                    dropPasteFiles: '%{browseFiles}',
                    browseFiles: 'Select at least 1 product image',
                },
            },
            hideUploadButton:true,
        })
        .use(XHR, {
            endpoint: '/test'
        })
        .on('file-added', (file) => {
            console.log('Added file', file);
        });

        // initialize datepicker
        $('#datetime').daterangepicker({
            "singleDatePicker": true,
            "showDropdowns": true,
            "timePicker": true,
            "autoApply": true,
            locale: {
                format: 'YYYY-MM-DD hh:mm A'
            }
        });

        $('body').on('change','#datetime', function(){
            vueTable.product.datetime = $(this).val();
            console.log(vueTable.product);
        })

    }
}).mount('.content')
vueTable.getProductCategories();
tinymce.init({
    selector: 'textarea',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    setup: function(editor) {
        editor.on('change', function () {
            editor.save();
            editor.getElement().dispatchEvent(new Event('input'));
        });
    }
});


</script>
@stop
