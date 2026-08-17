function maskPhone(value) {
    const digits = value.replace(/\D/g, '').slice(0, 11);

    if (digits.length <= 10) {
        return digits.replace(/(\d{2})(\d{0,4})(\d{0,4})/, (_, ddd, part1, part2) => {
            let result = ddd ? `(${ddd}` : '';
            if (ddd.length === 2) result += ') ';
            result += part1;
            if (part2) result += `-${part2}`;
            return result;
        }).trim();
    }

    return digits.replace(/(\d{2})(\d{0,5})(\d{0,4})/, (_, ddd, part1, part2) => {
        let result = ddd ? `(${ddd}` : '';
        if (ddd.length === 2) result += ') ';
        result += part1;
        if (part2) result += `-${part2}`;
        return result;
    }).trim();
}

document.addEventListener('input', (event) => {
    if (event.target.matches('[data-mask="phone"]')) {
        event.target.value = maskPhone(event.target.value);
    }
});
