const parseFilterUrlold = (url) => {
	const parts = url.split('|');
	const filters = [];
	for (let i = 0; i < parts.length; i++) {
		const part = parts[i];
		const split = part.split(':');
		const obj = {};
		obj[split[0]] = split[1].split(',');
		filters.push(obj);
	}
	return filters;
}

const parseFilterUrl = (url) => {
	const parts = url.split('|');
	const filters = [];
	for (let i = 0; i < parts.length; i++) {
		const part = parts[i];
		const split = part.split(':');
		const obj = {};
		obj[split[0]] = split[1].split(',');
		filters.push(obj);
	}
	return filters;
}

const filters = parseFilterUrl('regions:the-north|people:hodor,the-hound|omg:true');
console.log({filters});


function recursion(idx, parts, output) {
	if (idx == parts.length) {
		return
	}

	let part = parts[idx].split(':') // ['regions', 'the-north']
	let partKey = part[0] // regions
	let partValue = part[1].split(',') // the-north
	output.push({[partKey] : partValue}) // {regions : 'the-north'}

	recursion(idx+1, parts, output)
}

function filterTheUrl(parts) {	
	let output = []
	recursion(0, parts, output)
	return output
}

function getFilteredUrl(url) {
	let filteredUrl = []
	if (url.length) {
		filteredUrl = filterTheUrl(url.split('|')) // ['regions:the-north', 'people:hodor,the-hound', 'omg:true']
	}
	return filteredUrl
}


console.log('getFilteredUrl')
console.log(getFilteredUrl('regions:the-north|people:hodor,the-hound|omg:true'))