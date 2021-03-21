<template>
    <div class="card">
        <div class="card-header">Skills</div>

        <div class="card-body">
            <div v-for="(skill, index) in inputs" :key="index" class="d-flex mb-2 p-0">
                <div class="col-10"> 
                    <input 
                    placeholder="Type your skill"
                    class="form-control" 
                    type="text"
                    name="skills[]"
                    v-model="skills[index]">
                </div>
                <div clas="col-2">
                    <button class="btn btn-danger" @click.prevent="remove(index)">Delete</button>
                </div>
            </div>
            <div>
                <button class="ml-3 btn btn-primary" @click.prevent="add">Add</button>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        //props: ['skills'],
        props:{
            skills: {
                type: Array,
                default: () => ([])
            }
        },
        data() {
            return {
                inputs: this.skills
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        methods: {
            add(){
                this.skills.push('');
            },
            remove(index){
                this.skills.splice(index, 1);
            }
        },
    }
</script>
