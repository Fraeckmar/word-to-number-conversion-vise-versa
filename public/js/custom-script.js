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



// Dummy
var Small = {
	'zero': 0,
	'one': 1,
	'two': 2,
	'three': 3,
	'four': 4,
	'five': 5,
	'six': 6,
	'seven': 7,
	'eight': 8,
	'nine': 9,
	'ten': 10,
	'eleven': 11,
	'twelve': 12,
	'thirteen': 13,
	'fourteen': 14,
	'fifteen': 15,
	'sixteen': 16,
	'seventeen': 17,
	'eighteen': 18,
	'nineteen': 19,
	'twenty': 20,
	'thirty': 30,
	'forty': 40,
	'fifty': 50,
	'sixty': 60,
	'seventy': 70,
	'eighty': 80,
	'ninety': 90
};

var Magnitude = {
	'thousand':     1000,
	'million':      1000000,
	'billion':      1000000000,
	'trillion':     1000000000000,
	'quadrillion':  1000000000000000,
	'quintillion':  1000000000000000000,
	'sextillion':   1000000000000000000000,
	'septillion':   1000000000000000000000000,
	'octillion':    1000000000000000000000000000,
	'nonillion':    1000000000000000000000000000000,
	'decillion':    1000000000000000000000000000000000,
};

var a, n, g;

function text2num(s) {
	a = s.toString().split(/[\s-]+/);
	n = 0;
	g = 0;
	a.forEach(feach);
	return n + g;
}

function feach(w) {
	var x = Small[w];
	if (x != null) {
			g = g + x;
	}
	else if (w == "hundred") {
			g = g * 100;
	}
	else {
			x = Magnitude[w];
			if (x != null) {
					n = n + g * x
					g = 0;
			}
			else { 
					alert("Unknown number: "+w); 
			}
	}
}

console.log('text2num')
console.log(text2num('one thousand ninety-nine'));