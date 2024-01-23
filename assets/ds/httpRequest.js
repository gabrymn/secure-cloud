export default async function http_request(url, method, formData, callback_success, callback_failure) {

    try {
        const response = await fetch(url, 
        {
            method: method,
            body: formData,
        });
        const result = await response.json();
        await callback_success(result);

    } catch (error) {
        await callback_failure(error);
    }
}
