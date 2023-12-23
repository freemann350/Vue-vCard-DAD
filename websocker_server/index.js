const httpServer = require('http').createServer()
const io = require("socket.io")(httpServer, {
    cors: {
        // The origin is the same as the Vue app domain. Change if necessary
        origin: "http://localhost:5174",
        methods: ["GET", "POST"],
        credentials: true
    }
})

httpServer.listen(8080, () =>{
    console.log('listening on *:8080')
})


io.on('connection', (socket) => {
    console.log(`client ${socket.id} has connected`)

    socket.on('loggedIn', function (user) {
        socket.join(user.username)
        if (user.user_type == 'A') {
            socket.join('administrator')
        }
    })

    socket.on('loggedOut', function (user) {
        socket.leave(user.id)
        socket.leave('administrator')
    })

    socket.on('insertedAdmin', function (admin) {
        socket.in('administrator').emit('insertedAdmin', admin)
    })

    socket.on('vCardUpdated', function (vCard) {
        socket.in(vCard.phone_number).emit('vCardUpdated', vCard)
    })

    socket.on('vCardBlockedOrDeleted', function (vCard) {
        socket.in(vCard.phone_number).emit('vCardBlockedOrDeleted')
    })
    
    socket.on('vCardTransactionNotify', function (transactionToSave) {
        socket.in(transactionToSave.payment_reference).emit('vCardTransactionNotify', transactionToSave.value)
    })

    socket.on('vCardAddCredit', function (transactionToSave) {
        socket.in(transactionToSave.vcard).emit('vCardAddCredit', transactionToSave.new_balance)
    })

})
