<template>
    <div class="{{ $data['plural_lower'] }}">

        <div class="card">
            <div class="card-header">
                <h6 v-if="newRow == true" >Create {{$data['singular_lower']}}</h6>
                <h6 v-if="newRow == false" >List {{ $data['plural_lower'] }}</h6>
                 
            </div>
            <div class="card-body">
                <a href class="btn btn-success btn-sm" title="Add New " v-if="newRow == false" @click.prevent="addNew">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add New
                </a>
                <form @submit.prevent="create{{ $data['singular'] }}" v-if="newRow == true">

                    @foreach($data['fields'] as $field)
                    <div class="form-group">
                        @if($field['name'] == 'id' || $field['name'] == 'updated_at' || $field['name'] == 'created_at' )
                        <input type="hidden" v-model="form.{{$field['name']}}">
                        @elseif($field['simplified_type'] == 'text')
                        <label>{{   ucfirst(str_replace('_', ' ', $field['name'])) }}</label>
                        <input type="text" v-model="form.{{$field['name']}}" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('{{$field['name']}}') }" @if($field['max'])
                            maxlength="{{$field['max']}}" @endif>
                        @if($field['required'] && $field['name'] !== 'id')
                        <has-error :form="form" field="{{$field['name']}}"></has-error>
                        @endif
                        @elseif($field['simplified_type'] == 'textarea')
                        <label>{{   ucfirst(str_replace('_', ' ', $field['name'])) }}</label>
                        <textarea v-model="form.{{$field['name']}}" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('{{$field['name']}}') }" @if($field['max'])
                            maxlength="{{$field['max']}}" @endif></textarea>
                        @if($field['required'] && $field['name'] !== 'id')
                        <has-error :form="form" field="{{$field['name']}}"></has-error>
                        @endif
                        @else
                        <label>{{   ucfirst(str_replace('_', ' ', $field['name'])) }}</label>
                        <input type="number" class="form-control"
                            :class="{ 'is-invalid': form.errors.has('{{$field['name']}}') }"
                            v-model="form.{{$field['name']}}">
                        @if($field['required'] && $field['name'] !== 'id')
                        <has-error :form="form" field="{{$field['name']}}"></has-error>
                        @endif
                        @endif
                    </div>
                    @endforeach

                    <div class="form-group">
                        <button class="btn btn-success" type="submit" :disabled="form.busy"
                            name="button">@{{ (form.busy) ? 'Please wait...' : 'Submit'}}</button>
                    </div>
                </form>

            </div><!-- End first half -->

            <div class="col-12" v-if="newRow == false">
                <div class="table-responsive">
                    <table class="table table-bordered" v-if="{{ $data['plural_lower'] }}.length > 0">
                        <tr>
                            @foreach($data['fields'] as $field)
                            <td> {{   ucfirst(str_replace('_', ' ', $field['name'])) }} </td>
                            @endforeach
                            <td>Action</td>
                        </tr>
                        <tr v-for="({{ $data['singular_lower'] }},index) in {{ $data['plural_lower'] }}" :key="{{ $data['singular_lower'] }}.id">
                            @foreach($data['fields'] as $field)
                            @if ($field['name'] == 'id')
                            <td> @{{ index+1 }} </td>
                            @else    
                            <td> @{{ {!!$data['singular_lower'].'.'.$field['name']!!} }} </td>
                            @endif
                            @endforeach
                            <td>
                                <router-link :to="'/{{ $data['singular_lower'] }}/'+{{ $data['singular_lower'] }}.id"
                                    class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></router-link>
                                <button @click.prevent="delete{{$data['singular']}}({{ $data['singular_lower'] }},index)"
                                    type="button" :disabled="form.busy" name="button"
                                    class="btn btn-danger btn-sm"><i class="fa fa-trash" v-if="form.busy==false"> </i> @{{ (form.busy) ? 'Please wait...' : ''}}</button>
                            </td>
                        </tr>
                    </table>
                
                <span v-else-if="!{{ $data['plural_lower'] }}">Loading...</span>
                <span v-else>No {{ $data['plural_lower'] }} exist</span>

            </div><!-- End 2nd half -->
            </div>
        </div>
    </div>
</template>

<script>
import { Form, HasError, AlertError } from 'vform'
    export default {
        name: '{{ $data['singular'] }}',
        components: { HasError },
        data: function () {
            return {
                newRow: false,
                 {{$data['plural_lower']}}: false,
                form: new Form({
                    @foreach($data['fields'] as $field)
                    "{{$field['name']}}": "",
                    @endforeach
                })
            }
        },
        created: function(){
            this.list{{$data['plural']}}();
        },
        methods: {
            list{{ $data['plural'] }}: function(){
            
            var that = this;
            this.form.get('{{config('vueApi.vue_url_prefix')}}/{{ $data['plural_lower'] }}').then(function(response){
                that.{{ $data['plural_lower'] }} = response.data;
            })
            
            },
            create{{ $data['singular'] }}: function(){
      
            var that = this;
            this.form.post('{{config('vueApi.vue_url_prefix')}}/{{ $data['plural_lower'] }}').then(function(response){
                that.{{ $data['plural_lower'] }}.push(response.data);
            })
            this.newRow = false;
            @foreach($data['fields'] as $field)
            this.form.{{ $field['name']}} = '';
            @endforeach
            },
            delete{{$data['singular']}}: function({{ $data['singular_lower'] }}, index){
      
            var that = this;
            this.form.delete('{{config('vueApi.vue_url_prefix')}}/{{ $data['plural_lower'] }}/'+{{ $data['singular_lower'] }}.id).then(function(response){
                that.{{ $data['plural_lower'] }}.splice(index,1);
            })
                    
            },
            addNew: function (post, index) {
            this.newRow = true;
            },
        }
    }

</script>
