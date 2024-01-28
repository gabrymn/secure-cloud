export default async function httpRequest(url, method, formData, callbackSuccess, callbackFsailure) {

    try {
        const response = await fetch(url, 
        {
            method: method,
            body: formData,
        });
        const result = await response.json();
        await callbackSuccess(result);

    } catch (error) {
        await callbackFailure(error);
    }
}
