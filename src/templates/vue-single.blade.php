<template>
      <div class="{{ $data['singular'] }}Single">
        <div class="card">
          <div class="card-header">
            <h6>Update {{ $data['singular'] }}</h6>
          </div> 
          <div class="card-body">
        <form @submit.prevent="update{{$data['singular']}}" v-if="loaded">
          
          <router-link to="/{{ $data['plural_lower'] }}"  class="btn btn-warning btn-sm"> <i class="fa fa-arrow-left" aria-hidden="true"></i>
             Back to {{ $data['plural_lower'] }}</router-link>
          
@foreach($data['fields'] as $field)
            <div class="form-group">
@if($field['name'] == 'id' || $field['name'] == 'updated_at' || $field['name'] == 'created_at' )    
@elseif($field['simplified_type'] == 'text')
                  <label>{{   ucfirst(str_replace('_', ' ', $field['name'])) }}</label>
                  <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('{{ $field['name'] }}') }" v-model="form.{{$field['name']}}" @if($field['max']) maxlength="{{$field['max']}}" @endif>
@if($field['required'] && $field['name'] !== 'id')
                  <has-error :form="form" field="{{$field['name']}}"></has-error>
@endif
@elseif($field['simplified_type'] == 'textarea')
                  <label>{{   ucfirst(str_replace('_', ' ', $field['name'])) }}</label>
                  <textarea v-model="form.{{$field['name']}}" class="form-control" :class="{ 'is-invalid': form.errors.has('{{ $field['name'] }}') }" @if($field['max']) maxlength="{{$field['max']}}" @endif></textarea>
@if($field['required'] && $field['name'] !== 'id')
                  <has-error :form="form" field="{{$field['name']}}"></has-error>
@endif
@else
                  <label>{{   ucfirst(str_replace('_', ' ', $field['name'])) }}</label>
                  <input type="number" v-model="form.{{$field['name']}}" class="form-control" :class="{ 'is-invalid': form.errors.has('{{ $field['name'] }}') }">
@if($field['required'] && $field['name'] !== 'id')
                  <has-error :form="form" field="{{$field['name']}}"></has-error>
@endif
@endif
            </div>
@endforeach
      
          <div class="form-group">
              <button  class="btn btn-success btn-sm" type="submit" :disabled="form.busy" name="button">@{{ (form.busy) ? 'Please wait...' : 'Update'}}</button>
              <button class="btn btn-danger btn-sm" @click.prevent="delete{{$data['singular']}}"><i class="fa fa-trash" v-if="form.busy==false"></i> @{{ (form.busy) ? 'Please wait...' : ''}}</button>
          </div>
        </form>
        
        <span v-else>Loading {{ $data['singular_lower'] }}...</span>
      </div>
      </div>
    </div>
</template>

<script>
  import { Form, HasError, AlertError } from 'vform'
  export default {
    name: '{{ $data['singular'] }}',
    components: {HasError},
    data: function(){
      return {
        loaded: false,
        form: new Form({
  @foreach($data['fields'] as $field)
  @if($field['name'] == 'id' || $field['name'] == 'updated_at' || $field['name'] == 'created_at' )
  @else
            "{{$field['name']}}" : "",
  @endif
  @endforeach        
        })
      }
    },
    created: function(){
      this.get{{$data['singular']}}();
    },
    methods: {
      get{{$data['singular']}}: function({{$data['singular']}}){
        
        var that = this;
        this.form.get('{{config('vueApi.vue_url_prefix')}}/{{ $data['plural_lower'] }}/'+this.$route.params.id).then(function(response){
          that.form.fill(response.data);
          that.loaded = true;
        }).catch(function(e){
            if (e.response && e.response.status == 404) {
                that.$router.push('/404');
            }
        });
        
      },
      update{{$data['singular']}}: function(){
        
        var that = this;
        this.form.put('{{config('vueApi.vue_url_prefix')}}/{{ $data['plural_lower'] }}/'+this.$route.params.id).then(function(response){
          that.form.fill(response.data);
        })
        
      },
      delete{{$data['singular']}}: function(){
        
        var that = this;
        this.form.delete('{{config('vueApi.vue_url_prefix')}}/{{ $data['plural_lower'] }}/'+this.$route.params.id).then(function(response){
          that.form.fill(response.data);
          that.$router.push('/{{$data['plural_lower']}}');
        })
        
      }
    }
  }
  </script>