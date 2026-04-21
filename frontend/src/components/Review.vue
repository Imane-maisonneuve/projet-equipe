<template>
	<div class="review-box">
		<div class="rating">
			<button 
				v-for="n in 5" 
				:key="n" 
				@click="setRating(n)"
				:class="{ 'active': n <= rating }"
				type="button"
			>
				★
			</button>
		</div>

		<textarea 
			v-model="comment" 
			@input="debouncedSave"
			placeholder="Notes de dégustation..."
		></textarea>
	</div>
</template>

<script setup>
import { ref, watchEffect } from 'vue';

const props = defineProps(['initialRating', 'initialComment']);
const emit = defineEmits(['save']);

const rating = ref(0);
const comment = ref('');
let debounceTimer = null;

watchEffect(() => {
	rating.value = props.initialRating || 0;
	comment.value = props.initialComment || '';
});

const setRating = (n) => {
	rating.value = n;
	sendReview();
};

const debouncedSave = () => {
	clearTimeout(debounceTimer);
	debounceTimer = setTimeout(() => {
		sendReview();
	}, 800);
};

const sendReview = () => {
	emit('save', {
		rating: rating.value,
		comment: comment.value
	});
};
</script>

<style scoped>
.review-box {
	background: var(--blanc-clair);
	padding: 20px;
	border: 1px solid var(--gris-moyen);
	border-radius: 5px;
	margin-top: 15px;
}

.rating {
	display: flex;
	gap: 5px;
	margin-bottom: 10px;
}

.rating button {
	font-size: 28px;
	background: none;
	border: none;
	cursor: pointer;
	color: var(--gris-moyen);
	padding: 0;
	transition: color 0.2s ease;
}

.rating button.active {
	color: var(--bordeau-profond);
}

textarea {
	width: 100%;
	height: 100px;
	margin-top: 5px;
	padding: 10px;
	box-sizing: border-box;
	border: 1px solid var(--gris-moyen);
	border-radius: 5px;
	font-size: 14px;
	color: var(--noir-profond);
	resize: none;
	outline: none;
}

textarea:focus {
	border-color: var(--bordeau-profond);
}

textarea::placeholder {
	color: var(--gris-moyen);
	font-style: italic;
}
</style>