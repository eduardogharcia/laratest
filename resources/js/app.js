import "./bootstrap";
import axios from "axios";

const form = document.querySelector(".main-form");
const tableArea = document.querySelector(".table-area");

renderTable();

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    await handleFormSubmit({
        name: e.target.name.value,
        quantity: e.target.quantity.value,
        price: e.target.price.value,
    });

    renderTable();
});

async function handleFormSubmit({ name, quantity, price }) {
    await axios.post("/api/product", {
        name,
        quantity,
        price,
    });
}

async function renderTable () {
    const productList = await loadProducts();
    let table = '';
    let sum = 0;

    productList.forEach(({name, quantity, price, datetime, total}) => {
        table += `
            <tr>
                <td>${name}</td>
                <td>${quantity}</td>
                <td>${price}</td>
                <td>${new Date(datetime * 1000)}</td>
                <td>${total}</td>
            </tr>
        `;

        sum += Number(total);
    })

    table += `
        <tr>
            <td colspan="4" style="font-weight: bold">Total</td>
            <td style="font-weight: bold">${sum}<td>
        </td>
    `

    tableArea.innerHTML = table;
}

async function loadProducts() {
    const response = await axios.get('/api/product');
    return response.data;
}
