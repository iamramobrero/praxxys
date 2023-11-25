@extends('adminlte::page')
@section('title',  $pageTitle)

@section('content_header')
    <h1>{{$pageTitle}}</h1>
@stop

@section('content')
<div class="container position-relative">
    <div class="row mb-5">
        <div class="col text-center">
            <button v-for="n in 3" class="btn btn-large btn-border" v-bind:class="(isWizardStepComplete(n) ? 'text-success':'')">
                Step @{{n}}<br>
                <i class="fas fa-check" v-if="isWizardStepComplete(n)"></i>
            </button>
        </div>
    </div>
    <div class="row" v-bind:class="isCurrentWizardStep(3)">
        <div class="col">
            step 3
        </div>
    </div>
    <div class="row" v-bind:class="isCurrentWizardStep(1)">
        <div class="col">
            step 2
        </div>
    </div>
    <div class="row" v-bind:class="isCurrentWizardStep(2)">
        <div class="col-12">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                    <input class="form-control" v-bind:class="((validated && !product.category_id) ? 'is-invalid':null )"type="text" v-model="product.name" name="name" required>
                    <div class="invalid-feedback">
                        Please provide a valid name.
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Category</label>
                <div class="col-sm-10">
                    <select class="form-control" v-model="product.category_id" name="category_id" v-bind:class="((validated && !product.category_id) ? 'is-invalid':null )">
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
                    <textarea class="form-control" type="text" v-model="product.description" name="description" v-bind:class="((validated && !product.description) ? 'is-invalid':null )"></textarea>
                    <div class="invalid-feedback">
                        Please provide a description
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-10 offset-2">
                    <button class="btn btn-primary btn-sm" @click="changePage(2)">Next</button>
                </div>
            </div>
        </div>
    </div>
    <div id="files-drag-drop"></div>
</div>
@stop

@section('css')

@stop
@section('js')
<script>


const { createApp } = Vue
vueTable = createApp({
    data() {
        return {
            validated:false,
            wizardStep:1,
            productCategories:{},
            product:{
                name:'',
                category_id:'',
                description:'',
                images:[],
                datetime:'',
            }
        }
    },
    methods : {
        isWizardStepComplete(wizardStep){
            if((wizardStep==1) && (this.$data.product.name && this.$data.product.category_id && this.$data.product.description ) && (this.$data.wizardStep > wizardStep))
                return true;
            else if((wizardStep==2) && (this.$data.product.images.length))
                return true;
            else if((wizardStep==2) && (this.$data.product.datetime))
                return true;
        },
        isCurrentWizardStep(wizardStep){
            return (this.$data.wizardStep == wizardStep) ? null:'d-none';
        },
        changePage(wizardStep){
            vueTable.validated = true;
            if(wizardStep==2){
                if(!vueTable.product.name || !vueTable.product.category_id || !vueTable.product.description ){
                    Toast.fire({
                        icon: 'error',
                        title: 'Please complete step 1'
                    });

                    return
                }
            }

            // reset
            window.setTimeout(() => {
                vueTable.validated = false;
            }, 3000)

            vueTable.wizardStep = wizardStep;

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
