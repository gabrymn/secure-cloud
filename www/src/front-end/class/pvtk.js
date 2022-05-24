export const getpk = () => localStorage.getItem('pvtk') || undefined
export const checkpk = () => {
    if (getpk() === undefined)
    {
        alert("Chiave di decifrazione mancante, stai per essere disconnesso.")
        window.location.href = "../../back-end/class/out.php"
    }
}