# Import Umum
import math
import re
from os import path
from datetime import datetime
# import MySQLdb
import mysql.connector

# Data Fetcher
import pandas as pd

# Numerical
import numpy as np

# Accuracy
from sklearn.metrics import accuracy_score, mean_squared_error, mean_absolute_error

# Scaler
from sklearn.preprocessing import MinMaxScaler

# Model, LSTM
from keras.models import Sequential, load_model
from keras.layers import Dense, LSTM

# Graph plot
import matplotlib.pyplot as plt

# Get Variable
import sys

# DB
# HOST = "localhost"
# USERNAME = "root"
# PASSWORD = ""
# DATABASE = "db_smartsys"
# mydb = mysql.connector.connect(
#   host="localhost",
#   user="root",
#   password="",
#   database="db_smartsys"
# )

# Kolom Dataset
FIELD_CLOSE = 'Pembuatan'
FIELD_DATE = 'Tanggal'

# Jumlah Sample
SAMPLE_TRAINED = 60

# Waktu Untuk Prakiraan
NEXT_PREDICTION = 6 * 30 * 24 * 60 * 60 * 1000

# Mendapatkan Dataframe atau Dataset
def getDataFrame(stock, dateRange):
  # Format Tanggal
  dateStart = datetime.strptime(dateRange[0], '%Y/%m/%d')
  dateEnd = datetime.strptime(dateRange[1], '%Y/%m/%d')
  dateStartStr = dateRange[0].replace('/', '-')
  dateEndStr = dateRange[1].replace('/', '-')

  # Menghapus karakter yang tidak penting
  safeStockName = re.sub(r'\W+', '', stock)
  filepath = 'c:/xampp/htdocs/SmartSys/public/machine/'
  stockname = (filepath + safeStockName)
  # Data frame
  df = None

  # Cek File CSV
  fileCsvExist = path.exists('{}.csv'.format(stockname))
  if not fileCsvExist:
    print('\nData Tidak Ditemukan')
  else:
    # Membaca Dataset & Mengubah Menjadi Timeseries
    dateParse = lambda x: datetime.strptime(x, "%Y-%m-%d")
    df = pd.read_csv('{}.csv'.format(stockname), header='infer', parse_dates=[FIELD_DATE], date_parser=dateParse)
    df.sort_values(by=FIELD_DATE)
  
    # Get minimum timestamp of CSV data
    dtMin = df.loc[df.index.min(), FIELD_DATE]
    dateMin = int(dtMin.date().strftime('%S'))
    dateMin = dateMin * 1000

    # Get maximum timestamp of CSV data
    dtMax = df.loc[df.index.max(), FIELD_DATE]
    dateMax = int(dtMax.date().strftime('%S'))
    dateMax = dateMax * 1000

    # Get start and end timestamp of input date by user
    startTs = int(dateStart.timestamp() * 1000)
    endTs = int(dateEnd.timestamp() * 1000)

    # Create mask/filter
    mask = (df[FIELD_DATE] > dateStartStr) & (df[FIELD_DATE] <= dateEndStr)

    # Return mask and origin
    return (df.loc[mask], df)

# MAIN PROGRAM
def main():
  stock = sys.argv[1]
  dateRange = [sys.argv[2], sys.argv[3]]
  percent = 80

  # Begin Process message
  print('')
  print('Nama Data : {}'.format(stock))
  print('Dimulai Tgl : {}, Sampai Tgl : {}'.format(dateRange[0], dateRange[1]))
  print('Persentase Data Training(%) : {}'.format(percent))

  # Format date
  dateStart = datetime.strptime(dateRange[0], '%Y/%m/%d')
  dateEnd = datetime.strptime(dateRange[1], '%Y/%m/%d')
  dateStartStr = dateRange[0].replace('/', '-')
  dateEndStr = dateRange[1].replace('/', '-')
  startTs = int(dateStart.timestamp() * 1000)
  endTs = int(dateEnd.timestamp() * 1000)

  # Get data frame
  df, dfOrigin = getDataFrame(stock, dateRange)
  print('masuk')
  # print(df)

  # Stock name, remove any non alpha-numeric char
  safeStockName = re.sub(r'\W+', '', stock)

  # Check Data Frame shape
  print('Jumlah Data : ' + str(df.shape))

  # prepare dataset and use only Close price value
  dataset = df.filter([FIELD_CLOSE]).values

  # Create len of percentage training set
  trainingDataLen = math.ceil((len(dataset) * percent) / 100)
  print('Jumlah Data Training : ' + str(trainingDataLen))

  # Scale the dataset between 0 - 1
  scaler = MinMaxScaler(feature_range=(0, 1))
  scaledData = scaler.fit_transform(dataset)
  
  # Scaled trained data
  trainData = scaledData[:trainingDataLen , :]

  # Split into trained x and y
  xTrain = []
  yTrain = []
  for i in range(SAMPLE_TRAINED, len(trainData)):
    xTrain.append(trainData[i-SAMPLE_TRAINED:i , 0])
    yTrain.append(trainData[i , 0])
  
  # Convert trained x and y as numpy array
  xTrain, yTrain = np.array(xTrain), np.array(yTrain)
  # print('x - y train shape: ' + str(xTrain.shape) + ' ' + str(yTrain.shape))

  # Reshape x trained data as 3 dimension array
  xTrain = np.reshape(xTrain, (xTrain.shape[0], xTrain.shape[1], 1))
  # print('Expected x train shape: ' + str(xTrain.shape))
  print('')

  print('Processing the LSTM model...\n')

  # Build LSTM model
  model = Sequential()
  model.add(LSTM(50, return_sequences=True, input_shape=(xTrain.shape[1], 1)))
  model.add(LSTM(50, return_sequences=False))
  model.add(Dense(25))
  model.add(Dense(1))

  # Compile model
  model.compile(optimizer='adam', loss='mean_squared_error')

  # Train the model
  model.fit(xTrain, yTrain, batch_size=1, epochs=1)

  print('\nDone Processing the LSTM model...')

  # Prepare testing dataset
  testData = scaledData[trainingDataLen - SAMPLE_TRAINED: , :]
  
  # Create dataset test x and y
  xTest = []
  yTest = dataset[trainingDataLen: , :]
  for i in range(SAMPLE_TRAINED, len(testData)):
    xTest.append(testData[i - SAMPLE_TRAINED:i, 0])
  
  # Convert test set as numpy array
  xTest = np.array(xTest)

  # Reshape test set as 3 dimension array
  xTest = np.reshape(xTest, (xTest.shape[0], xTest.shape[1], 1))

  # Models predict price values
  predictions = model.predict(xTest)
  predictions = scaler.inverse_transform(predictions)
  
  # Get root mean square (RMSE)
  rmse = np.sqrt(np.mean(predictions - yTest) ** 2)
  print('\nRoot mean square (RMSE) : ' + str(rmse))
  
  # Get mean square (MSE)
  mse = np.square(np.subtract(yTest,predictions)).mean()
  print('Mean square (MSE):' + str(mse))
  
  # Get  (MAE)
  mae = mean_absolute_error(yTest,predictions)
  print('Mean square (MAE):' + str(mae))
  
  # perhitungan Nilai SI
  avg = sum(predictions)/len(testData)
  SI = (rmse/avg) * 100
  print('Nilai SI : ', SI)
  print(' ')
  
  # Validasi Hasil Akhir Model
  # Create dataset test x and y
  xVal = []
  yVal = dataset[1: , :]
  
  for i in range(1, len(scaledData)):
    xVal.append(scaledData[i - 1:i, 0])

  # Convert test set as numpy array
  xVal = np.array(xVal)

  # Convert test set as numpy array
  xVal = np.array(xVal)

  # Reshape test set as 3 dimension array
  xVal = np.reshape(xVal, (xVal.shape[0], xVal.shape[1], 1))

  # Models predict price values
  ValPredictions = model.predict(xVal)
  ValPredictions = scaler.inverse_transform(ValPredictions)

  print('\n==== Hasil Validasi Prakiraan ====')
  # print('Jumlah Data : ', len(ValPredictions))

  # RMSE
  rmseval = np.sqrt(np.mean(ValPredictions - yVal) ** 2)
  print('Root mean square (RMSE) : ' + str(rmseval))

  # MSE
  mseval = np.square(np.subtract(yVal,ValPredictions)).mean()
  print('Mean square (MSE):' + str(mseval))

  # MAE
  maeval = mean_absolute_error(yVal,ValPredictions)
  print('Mean square (MAE):' + str(maeval))

  avg_yVal = np.mean(yVal)
  print('nilai rata2 data : ', avg_yVal)
  avg_predictionNew = np.mean(ValPredictions)
  print('nilai rata2 hasil prakiraan : ', avg_predictionNew)
  akurasi = 100 - (((avg_yVal - avg_predictionNew)/ avg_yVal) * 100)
  print('nilai akurasi : ', akurasi)
  
  # perhitungan Nilai SI
  avgval = sum(ValPredictions)/len(yVal)
  SIval = (rmseval/avgval) * 100
  print('Nilai SI : ', SIval)
  print('=============================')
  
  batasmin = int(sys.argv[4])
  if akurasi < batasmin:
    main()
  else:
    # namamodel = sys.argv[1] + '.h5'
    namamodel = 'my_model' + '.h5'
    # DB
    mydb = mysql.connector.connect(
      host="localhost",
      user="root",
      password="",
      database="db_smartsys"
    )
    sql_cek = "SELECT nilai_akurasi FROM tb_model WHERE nama_model = %s"
    cursor = mydb.cursor()
    cursor.execute(sql_cek, (namamodel,))
    nilaidb = cursor.fetchall()
    # nilaidb = float(record[0])
    # print(record)
    if nilaidb == []:
      id = 1
      print('nilai 0')
      model.save('c:/xampp/htdocs/SmartSys/public/model/' + namamodel)
      sql_insert = "INSERT INTO tb_model (id_barang, nama_model, nilai_akurasi) VALUES (%s, %s, %s) "
      record_insert = (id, namamodel, akurasi)
      cursor = mydb.cursor()
      cursor.execute(sql_insert, record_insert)
      mydb.commit()
    else:
      print('nilai 1')
      if nilaidb < akurasi:
        print('nilai 2')
        model.save('c:/xampp/htdocs/SmartSys/public/model/' + namamodel)
        sql_update = "UPDATE tb_model set nilai_akurasi = %s where namamodel = %s"
        input_data = (akurasi, namamodel)
        cursor = mydb.cursor()
        cursor.execute(sql_update, input_data)
        mydb.commit()
    # DB
    # connection = mysql.connector.connect(
    #   host="localhost",
    #   user="root",
    #   password="",
    #   database="db_smartsys"
    # )
    # sql_select_Query = "SELECT stok_barang FROM tb_barang WHERE nama_barang = %s"
    # cursor = connection.cursor()
    # cursor.execute(sql_select_Query, (namamodel))
    # # get all records
    # barang = cursor.fetchall()
    # for brg in barang:
    #   print(brg)
    # if myresult == 0:
    #   # if akurasi > akurasidb:
    #   print('nulll')
    # model.save('c:/xampp/htdocs/SmartSys/public/model/' + 'my_model.h5')
  
if __name__ == '__main__':
  main()