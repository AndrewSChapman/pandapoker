
<template>
    <div :class="{'animal': true, 'selected': selected }">
        <img v-if="isBird" src="@/assets/animals/bird.svg" width="50" />
        <img v-if="isCat" src="@/assets/animals/cat.svg" width="50" />
        <img v-if="isCow" src="@/assets/animals/cow.svg" width="50" />
        <img v-if="isDog" src="@/assets/animals/dog.svg" width="50" />
        <img v-if="isDuck" src="@/assets/animals/duck.svg" width="50" />
        <img v-if="isElephant" src="@/assets/animals/elephant.svg" width="50" />
        <img v-if="isGoat" src="@/assets/animals/goat.svg" width="50" />
        <img v-if="isLion" src="@/assets/animals/lion.svg" width="50" />
        <img v-if="isMonkey" src="@/assets/animals/monkey.svg" width="50" />
        <img v-if="isOwl" src="@/assets/animals/owl.svg" width="50" />
        <img v-if="isPig" src="@/assets/animals/pig.svg" width="50" />
        <img v-if="isRooster" src="@/assets/animals/rooster.svg" width="50" />
    </div>
</template>

<script lang="ts">
    // Components
    import { Component, Prop, Vue } from 'vue-property-decorator';
    import { AnimalType } from '@/domains/user/enums/AnimalType';

    @Component({
        components: {},
    })
    export default class MyComponent extends Vue {
        @Prop({ required: true })
        public animalType!: AnimalType;

        @Prop({ required: true, type: Boolean })
        public selected!: boolean;

        public get isBird(): boolean {
            return this.animalType === AnimalType.BIRD;
        }

        public get isCat(): boolean {
            return this.animalType === AnimalType.CAT;
        }

        public get isCow(): boolean {
            return this.animalType === AnimalType.COW;
        }

        public get isDog(): boolean {
            return this.animalType === AnimalType.DOG;
        }

        public get isDuck(): boolean {
            return this.animalType === AnimalType.DUCK;
        }

        public get isElephant(): boolean {
            return this.animalType === AnimalType.ELEPHANT;
        }

        public get isGoat(): boolean {
            return this.animalType === AnimalType.GOAT;
        }

        public get isLion(): boolean {
            return this.animalType === AnimalType.LION;
        }

        public get isMonkey(): boolean {
            return this.animalType === AnimalType.MONKEY;
        }

        public get isOwl(): boolean {
            return this.animalType === AnimalType.OWL;
        }

        public get isPig(): boolean {
            return this.animalType === AnimalType.PIG;
        }

        public get isRooster(): boolean {
            return this.animalType === AnimalType.ROOSTER;
        }
    }
</script>

<style lang="scss" scoped>
    @import "src/scss/globals";
</style>
