<style scoped>
.wrapper{
  display: flex;
  height: 90vh;
  border: 1px solid blue;
  margin: 0.5rem;
  padding: 0.5rem;
  position: relative;
  background: center / cover no-repeat;
}
.wrapper .col {
  display: flex;
  flex-grow: 1;
  flex-direction: column;
  justify-content: flex-end;
  overflow: hidden;
  background: rgb(0,0,0, 0);
  transition: all 1500ms;
  padding: 0.25rem;
}

.wrapper .col.active{
  background: rgb(0,0,0, 0);
  background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.2) 80%, rgba(0,0,0,1) 100%);
}

.wrapper .col > div {
  transition: all 1s;
  border: 1px solid red;
  padding:.25rem;
  text-align: center;
  font-size: 0.8rem;
  background-color: rgba(255,255,255,0.25);
}
.wrapper .col .col-title {
  flex: 0 1 0;
  font-size:1.25rem;
}
.wrapper .col .col-content {
  flex: 0 1 0;
  padding: 0;
}
.wrapper .col .col-content p {
  margin: 0;
  height:0 ;
}
  
.wrapper .col.active .col-content {
  flex: 1 0 0;
}



.wrapper .col:hover{
  background-color: rgba(0,0,0,.25)
}
.wrapper .col:hover > div{
  background-color: rgba(255,255,255,.75)
}

.wrapper,
.wrapper:before,
.wrapper .col,
.wrapper .col > div {
  border-radius: var(--border-radius); 
}

@media only screen and (max-width: 600px){
  .wrapper{
    flex-direction:column;
  }
}
</style>


<template>
  <div id="species-page-container">
   <div class="wrapper" :style="{ backgroundImage: `url(${img_url})` }">
     <div class="carousel-btns"><span>&lt;</span></div>
     <div
          class="col"
          v-for="col in cols"
          :key="col.id"
          :class="{'active': selectedCol == col.title}"
          v-on:click="selectedCol = (selectedCol == col.title) ? 'X' : col.title"
          >
          <!-- v-on:mouseleave="selectedCol = 'X'"           -->
       <div
            class="col-title"
            v-text="col.title"
       />
       <div class="col-content" :style="{visibility: selectedCol == col.title}">
         <p v-text="col.content" />
       </div>
     </div>
     <div class="carousel-btns"><span>&gt;</span></div>
   </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
export default defineComponent({
  data() {
    return {
      cols:[
        {
          id: 0,
          title: "Description",
          content: "species description"
        },
        {
          id: 1,
          title: "Taxonomy",
          content: "species taxonomy"
        },
        {
          id: 2,
          title: "Distribution",
          content: "species distribution"
        }
      ],
      selectedCol: "X",
      img_url: "https://static.inaturalist.org/photos/250555410/large.jpeg",
    };
  },
  methods: {
  }
})
</script>