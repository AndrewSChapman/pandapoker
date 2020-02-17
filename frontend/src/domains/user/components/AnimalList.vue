<template>
    <ul class="animalList">
        <li @click="selectAnimal('bird')">
            <Animal animal-type="bird" :selected="selectedAnimalType == 'bird'" />
        </li>
        <li @click="selectAnimal('cat')">
            <Animal animal-type="cat" :selected="selectedAnimalType == 'cat'" />
        </li>
        <li @click="selectAnimal('cow')">
            <Animal animal-type="cow" :selected="selectedAnimalType == 'cow'" />
        </li>
        <li @click="selectAnimal('dog')">
            <Animal animal-type="dog" :selected="selectedAnimalType == 'dog'" />
        </li>
        <li @click="selectAnimal('duck')">
            <Animal animal-type="duck" :selected="selectedAnimalType == 'duck'" />
        </li>
        <li @click="selectAnimal('elephant')">
            <Animal animal-type="elephant" :selected="selectedAnimalType == 'elephant'"  />
        </li>
        <li @click="selectAnimal('goat')">
            <Animal animal-type="goat" :selected="selectedAnimalType == 'goat'" />
        </li>
        <li @click="selectAnimal('lion')">
            <Animal animal-type="lion" :selected="selectedAnimalType == 'lion'" />
        </li>
        <li @click="selectAnimal('monkey')">
            <Animal animal-type="monkey" :selected="selectedAnimalType == 'monkey'" />
        </li>
        <li @click="selectAnimal('owl')">
            <Animal animal-type="owl" :selected="selectedAnimalType == 'owl'" />
        </li>
        <li @click="selectAnimal('pig')">
            <Animal animal-type="pig" :selected="selectedAnimalType == 'pig'" />
        </li>
        <li @click="selectAnimal('rooster')">
            <Animal animal-type="rooster" :selected="selectedAnimalType == 'rooster'" />
        </li>
    </ul>
</template>

<script lang="ts">
    // Components
    import { Component, Vue, Prop, Watch } from 'vue-property-decorator';
    import { AnimalType } from '@/domains/user/enums/AnimalType';
    import Animal from '@/domains/user/components/Animal.vue';
    import { AnimalHelper } from '@/domains/user/helpers/AnimalHelper';

    @Component({
        components: {Animal},
    })
    export default class AnimalList extends Vue {
        @Prop({ required: true })
        public selectedAnimalType!: AnimalType|null;

        public selectAnimal(animalType: AnimalType): void {
            AnimalHelper.playSound(animalType);
            this.$emit('clicked', animalType);
        }
    }
</script>

<style lang="scss" scoped>
    @import "src/scss/globals";
    @import "src/scss/mixins";

    @include spin;

    .animalList {
        list-style-type: none;
        margin: $margin-small 0;
        padding: 0;
    }

    li {
        list-style-type: none;
        margin: 0 $margin $margin 0;
        display: inline-block;

        .animal {
            padding: $padding;
            border: $input-border;
            opacity: 0.8;
            border-radius: $border-radius;
            transition: background-color 0.5s, opacity 0.5s;

            &:hover {
                opacity: 1;
                border-color: $positive;
                background-color: $violet;
                cursor: pointer;
                animation: spin 1.5s linear infinite;
            }

            &.selected {
                animation: none;
                border-color: $positive;
                background-color: $violet;
            }
        }
    }
</style>
