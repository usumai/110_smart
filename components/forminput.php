<template id="textinput">
<div>
    <input 
        v-if="!inputtype"
        type='text' 
        class="form-control"
        v-bind:style="'background-color:'+box_color"
        v-model='internal_val'
        v-on:change='commit_value'
        v-on:keyup='commit_value_pause'
        :disabled='disabled'
    >
    <input 
        v-if="inputtype=='date'"
        type='date' 
        class="form-control"
        v-bind:style="'background-color:'+box_color"
        v-model='internal_val'
        v-on:change='commit_value'
        v-on:keyup='commit_value_pause'
        :disabled='disabled'
        onkeydown='event.preventDefault()'
        :min='min_date'
        :max='max_date'
    >
    <textarea 
        v-if="inputtype=='textarea'"
        rows='3' 
        class="form-control"
        v-bind:style="'background-color:'+box_color"
        v-model='internal_val'
        v-on:change='commit_value'
        v-on:keyup='commit_value_pause'
        :disabled='disabled'
        ref='date'
        :data-name='column_name'
    ></textarea>

    <div 
        v-if="inputtype=='check'"
        class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn"  :class="{'btn-dark': internal_val==1, 'btn-outline-dark': internal_val!=1}">
            <input type="radio" name="options" v-on:click='save_checkbox(1)' :disabled='disabled'>On
        </label>
        <label class="btn"  :class="{'btn-dark': internal_val!=1, 'btn-outline-dark': internal_val==1}">
            <input type="radio" name="options" v-on:click='save_checkbox(0)' :disabled='disabled'>Off
        </label>
    </div>

    <div class="alert" role="alert" v-if="internal_msg" 
        v-bind:class="{'alert-danger': msg_color=='danger','alert-warning': msg_color=='warning','alert-success': msg_color=='success'}">
        {{ internal_msg }}
    </div>

    <div 
        v-if="inputtype=='checkyn'"
        class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn"  :class="{'btn-dark': internal_val==1, 'btn-outline-dark': internal_val!=1}">
            <input type="radio" name="options" v-on:click='save_checkbox(1)' :disabled='disabled'>Yes
        </label>
        <label class="btn"  :class="{'btn-dark': internal_val!=1, 'btn-outline-dark': internal_val==1}">
            <input type="radio" name="options" v-on:click='save_checkbox(0)' :disabled='disabled'>No
        </label>
    </div>

    <div class="alert" role="alert" v-if="internal_msg" 
        v-bind:class="{'alert-danger': msg_color=='danger','alert-warning': msg_color=='warning','alert-success': msg_color=='success'}">
        {{ internal_msg }}
    </div>
</div>

</template>





<script type="text/javascript">
function vdt(vval, vset){//Will return empty string or an error message
    if (!vset){//If no settings exist
        return ''
    }else if (vset.required&&!vval){
        return "This field is required"
    }else if (vset.maxLen&&vval.length>=vset.maxLen){
        return "Value cannot be longer than "+vset.maxLen+" characters."
    }else if (vset.minLen&&vval.length<=vset.minLen){
        return "Value must be longer than "+vset.minLen+" characters."
    }else if (vset.basicChars&&!/^[a-zA-Z0-9_]*$/.test(vval)){
        return "Value must be basic alphanumeric without spaces or special characters, underscores permitted"
    }else if (vset.numeric&&!/^-?\d*\.?\d*$/.test(vval)){
        return "Value must be numeric."
    }else if (vset.digits&&!/^\d+$/.test(vval)){
        return "Value must only consist of digits."
    }else if (vset.maxNum&&vval>vset.maxNum){
        return "Value cannot be higher than "+vset.maxNum+"."
    }else if (vset.minNum&&vval<vset.minNum){
        return "Value cannot be less than "+vset.minNum+"."
    //}else if (vset.isadate&&Date.parse(vval)){
    //  return "Value must be a valid date."
    }else{
        return ''
    }
}













Vue.component('textinput', {
    props: [
        'primary_key',
        'primary_key_name',
        'inputtype',
        'bound_value',
        'disabled', 
        'db_name',
        'table_name',
        'column_name',
        'maxlen',
        'required',
        'minlen',
        'basicchars',
        'numeric',
        'digits',
        'maxnum',
        'minnum',
        'min_date',
        'max_date',
        'follow_function',
    ],

    template: '#textinput',
    data: ()=>({
        internal_val:'',
        internal_msg:'',
        box_color:'',
        msg_color:'',
        full_table_name:'',
    }),

    methods: {
        refresh_page(){
            this.internal_val       = this.$props.bound_value
            
                
            if (this.inputtype=='date'){
                // console.log(this.internal_val)
                // this.internal_val = Date.parse(this.internal_val)
                // console.log(this.internal_val)
                // this.internal_val = New Date(this.internal_val)
            }
            //this.internal_val = '2020-08-18'
            this.full_table_name    = this.db_name + '.' + this.table_name
        },
        save_checkbox(save_val){
            this.internal_val = save_val
            this.save_value()
        },
        commit_value_pause(){
            //Do nothing for now
        },
        commit_value(){
            // console.log('saving')
            let validity_res    = this.assess_value()
            if (validity_res!='') {
                format = "error"
                disp_msg = validity_res
            } else { 
                let save_res = this.save_value()
                // console.log('save_res')
                // console.log(save_res)
                if (save_res){
                    format = 'success'
                    disp_msg = ''
                } else { 
                    format = 'warning'
                    disp_msg = 'Issue with saving'
                }
            }
            this.update_formatting(format, disp_msg)
        },
        assess_value(){
            //console.log("Validating value")
            vval = this.internal_val
            let vset = {
                    required:   this.$props.required,
                    maxLen:     this.$props.maxlen,
                    minLen:     this.$props.minlen,
                    basicChars: this.$props.basicchars,
                    numeric:    this.$props.numeric,
                    digits:     this.$props.digits,
                    maxNum:     this.$props.maxnum,
                    minNum:     this.$props.minnum,
                    isadate:    this.$props.inputtype=='date',
                }
            test = vdt(vval, vset)
            return test
        },
        save_value(){
            //this.column_name = field_name
            //console.log("Attempting to save")
            this.full_table_name    = this.db_name + '.' + this.table_name
            //console.log(this.full_table_name)
            payload = { 'act':'save_textinput', 
                        'full_table_name':this.full_table_name, 
                        'column_name':this.column_name, 
                        'primary_key_name':this.primary_key_name, 
                        'primary_key':this.primary_key, 
                        'internal_val':this.internal_val, 
                        'indexpage':true}
            //console.log(payload)
            json    = fnapi(payload)
            save_res= json==1 ? true : false
            this.$root.$emit('follow_function',this.follow_function)
            return save_res
        },
        update_formatting(format, msg){
            //console.log("Updating formatting")
            this.internal_msg = msg
            if (format=='error') {
                this.box_color = 'pink'
                this.msg_color = 'danger'
            } else if (format=='warning') {
                this.box_color = 'Moccasin'
                this.msg_color = 'warning'
            } else if (format=='success') {
                this.box_color = 'HoneyDew'
                this.msg_color = 'success'
            
            } else { 
                this.box_color = ''
                this.msg_color = ''
            }
        },
    },
    watch: {
        bound_value: {//This is provided by AJAX from parent, and it isn't available immediately
            handler: function(new_val) {
                this.refresh_page()
            },
        }
    },
    mounted(){
        this.refresh_page()
    }
})
</script>